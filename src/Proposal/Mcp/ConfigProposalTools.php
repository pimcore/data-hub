<?php

declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Proposal\Mcp;

use function bin2hex;
use function implode;
use function is_array;
use function json_decode;
use function json_encode;
use const JSON_INVALID_UTF8_SUBSTITUTE;
use Mcp\Schema\Content\TextContent;
use Mcp\Schema\Result\CallToolResult;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigDocument;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigProposalPolicyInterface;
use Pimcore\Bundle\DataHubBundle\Utils\Constants\PermissionConstants;
use Pimcore\Bundle\PimcoreAgentBundle\Proposal\ProposalWidgetEmitter;
use Pimcore\Bundle\PimcoreAgentBundle\Security\BoundSessionReferenceResolver;
use Pimcore\Bundle\PimcoreAgentBundle\Service\AgentSessionServiceInterface;
use Pimcore\Bundle\StudioBackendBundle\Mcp\Exception\InvalidMcpToolArgumentException;
use Pimcore\Bundle\StudioBackendBundle\Mcp\Tool\McpToolErrorHandlerInterface;
use Pimcore\Bundle\StudioBackendBundle\Security\Service\SecurityServiceInterface;
use Pimcore\Model\User;
use function random_bytes;
use function sprintf;
use Symfony\Component\Yaml\Yaml;
use Throwable;

/**
 * What an adapter's list, get and propose tools do, so each adapter only declares its tools'
 * names and descriptions — an agent picks a tool by those, so they stay the adapter's own.
 *
 * MCP tools bypass the #[IsGranted] / kernel.exception pipeline the Studio controllers rely
 * on, so every call gates itself here.
 */
final readonly class ConfigProposalTools
{
    private const string PROPOSAL_TYPE = 'subject-update';

    private const string CODE_INVALID_REQUEST = 'invalid_request';

    private const string CODE_INTERNAL_ERROR = 'internal_error';

    private const string CODE_NOT_FOUND = 'not_found';

    private const string CODE_PERMISSION_DENIED = 'permission_denied';

    public function __construct(
        private AgentSessionServiceInterface $sessionService,
        private ProposalWidgetEmitter $widgetEmitter,
        private BoundSessionReferenceResolver $boundSessionReferenceResolver,
        private SecurityServiceInterface $securityService,
        private McpToolErrorHandlerInterface $errorHandler,
    ) {
    }

    /**
     * The configurations of the policy's kind the current user may read.
     */
    public function list(ConfigProposalPolicyInterface $policy, string $tool): CallToolResult
    {
        $user = $this->allowedUser();
        if ($user === null) {
            return $this->denied();
        }

        try {
            $configurations = [];
            foreach (Configuration::getList() as $configuration) {
                if ($configuration->getType() !== $policy->configType()
                    || !$configuration->isAllowed(PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_READ, $user)) {
                    continue;
                }

                $document = $configuration->getConfiguration();
                $general = $document['general'] ?? [];
                $configurations[] = [
                    'name' => $configuration->getName(),
                    'group' => $general['group'] ?? '',
                    'description' => $general['description'] ?? '',
                    'active' => (bool) ($general['active'] ?? false),
                    ...$policy->summarize($document),
                ];
            }
            $result = $this->successResult(['configurations' => $configurations]);
        } catch (Throwable $e) {
            $result = $this->handledError($e, $tool);
        }

        return $result;
    }

    /**
     * One configuration, as the document a proposal sends back. Withheld paths are left out:
     * handing them to a model would only invite it to send them back.
     */
    public function get(ConfigProposalPolicyInterface $policy, string $tool, string $name): CallToolResult
    {
        $user = $this->allowedUser();
        if ($user === null) {
            return $this->denied();
        }

        try {
            $configuration = $this->load($policy, $name);
            $result = $configuration === null
                || !$configuration->isAllowed(PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_READ, $user)
                ? $this->errorResult(sprintf('No %s named "%s".', $policy->noun(), $name), self::CODE_NOT_FOUND)
                : $this->successResult([
                    'name' => $name,
                    'configuration' => ConfigDocument::readable($configuration->getConfiguration(), $policy),
                    'withheld' => $policy->withheld(),
                ]);
        } catch (Throwable $e) {
            $result = $this->handledError($e, $tool, ['name' => $name]);
        }

        return $result;
    }

    /**
     * Records a configuration — a change to one, or a new one — as a proposal the user reviews,
     * instead of writing it.
     *
     * @param array<string, mixed>|string $configuration
     */
    public function propose(
        ConfigProposalPolicyInterface $policy,
        string $tool,
        string $name,
        array|string $configuration,
        ?string $summary,
    ): CallToolResult {
        $user = $this->allowedUser();
        if ($user === null) {
            return $this->denied();
        }

        try {
            $result = $this->record($policy, $user, $name, $configuration, $summary);
        } catch (Throwable $e) {
            $result = $this->handledError($e, $tool, ['name' => $name]);
        }

        return $result;
    }

    /**
     * @param array<string, mixed>|string $configuration
     */
    private function record(
        ConfigProposalPolicyInterface $policy,
        User $user,
        string $name,
        array|string $configuration,
        ?string $summary,
    ): CallToolResult {
        $sessionId = $this->boundSessionReferenceResolver->resolve();
        $existing = $this->load($policy, $name);
        $proposed = $this->decode($configuration);
        // folded only once the sections are known to belong: a refused one never reaches a state
        $state = $proposed === null
            || ConfigDocument::unknownSections($proposed, $policy) !== []
            || ConfigDocument::refusedPaths($proposed, $policy) !== []
            ? null
            : $this->stateFor($policy, $name, $existing, $proposed);

        $problem = $this->problemWith($policy, $user, $sessionId, $name, $existing, $proposed, $state);
        if ($problem !== null || $sessionId === null || $state === null) {
            return $this->errorResult($problem ?? 'No chat session context.');
        }

        if ($existing === null) {
            // a configuration nobody switched on must not go live because it was reviewed
            $state['general']['active'] ??= false;
            $state['general']['path'] ??= '';
        }

        $proposalId = bin2hex(random_bytes(16));
        $label = $existing === null
            ? sprintf('A new %s %s', $name, $policy->noun())
            : sprintf('Proposed changes to the %s %s', $name, $policy->noun());

        $this->sessionService->setProposalData($sessionId, $proposalId, [
            'proposalType' => self::PROPOSAL_TYPE,
            'subjectType' => $policy->subjectType(),
            'subjectRef' => $name,
            'subjectState' => $state,
            'label' => $label,
            // the card and the review title say "new" rather than "changed" on this
            'isNew' => $existing === null,
        ], (int) $user->getId());

        $result = ['proposalId' => $proposalId, 'name' => $name];
        $result += $this->widgetEmitter->buildAutoEmit(
            self::PROPOSAL_TYPE,
            [$proposalId],
            $summary ?? $label,
            null,
            $sessionId,
        );

        return $this->successResult($result);
    }

    /**
     * The stored document with the proposal folded in, under the subject's own identity. A name
     * nothing is stored under creates, so there the proposal is the whole configuration.
     *
     * @param array<string, mixed> $proposed
     *
     * @return array<string, mixed>
     */
    private function stateFor(
        ConfigProposalPolicyInterface $policy,
        string $name,
        ?Configuration $existing,
        array $proposed,
    ): array {
        $stored = ConfigDocument::readable($existing?->getConfiguration() ?? [], $policy);
        $state = ConfigDocument::fold($stored, $proposed);

        // the subject strips these from its own state; proposing them back adds leaves to the
        // review that name a change nobody made
        foreach (ConfigDocument::VOLATILE_GENERAL as $volatile) {
            unset($state['general'][$volatile]);
        }

        // identity and adapter type belong to the subject, never to a proposal
        $state['general']['name'] = $name;
        $state['general']['type'] = $existing?->getType() ?? $policy->configType();

        return $state;
    }

    /**
     * The first reason this proposal cannot be recorded, in the order the agent should fix them.
     *
     * @param array<string, mixed>|null $proposed
     * @param array<string, mixed>|null $state
     */
    private function problemWith(
        ConfigProposalPolicyInterface $policy,
        User $user,
        ?string $sessionId,
        string $name,
        ?Configuration $existing,
        ?array $proposed,
        ?array $state,
    ): ?string {
        $checks = [
            fn (): ?string => $sessionId === null ? 'No chat session context.' : null,
            // a configuration the user may not open is one they may not propose onto either
            fn (): ?string => $existing !== null
                && !$existing->isAllowed(PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_READ, $user)
                ? sprintf('No %s named "%s".', $policy->noun(), $name)
                : null,
            fn (): ?string => $existing === null && !ConfigDocument::isValidName($name)
                ? sprintf(
                    '"%s" cannot name a configuration: use letters, digits, "-" and "_", '
                    . 'starting with a letter or digit.',
                    $name,
                )
                : null,
            fn (): ?string => $proposed === null
                ? 'The configuration must be an object, or a JSON or YAML object string.'
                : null,
            fn (): ?string => self::listed(
                ConfigDocument::unknownSections($proposed ?? [], $policy),
                '%s is not part of a ' . $policy->noun() . '. Read the current document with '
                . $policy->readToolName() . ' and send that back with your changes applied, rather '
                . 'than writing one from memory.',
            ),
            fn (): ?string => self::listed(
                ConfigDocument::refusedPaths($proposed ?? [], $policy),
                '%s cannot be proposed. ' . $policy->withheldReason() . ' Send the document back '
                . 'without them.',
            ),
            fn (): ?string => self::listed(
                $state === null ? [] : $policy->problems($state, $existing === null),
                '%s',
                "\n",
            ),
        ];

        foreach ($checks as $check) {
            $problem = $check();
            if ($problem !== null) {
                return $problem;
            }
        }

        return null;
    }

    /**
     * @param list<string> $items
     */
    private static function listed(array $items, string $message, string $glue = ', '): ?string
    {
        return $items === [] ? null : sprintf($message, implode($glue, $items));
    }

    /**
     * @param array<string, mixed>|string $configuration
     *
     * @return array<string, mixed>|null
     */
    private function decode(array|string $configuration): ?array
    {
        if (is_array($configuration)) {
            return $configuration;
        }

        $decoded = json_decode($configuration, true);
        if (!is_array($decoded)) {
            try {
                $decoded = Yaml::parse($configuration);
            } catch (Throwable) {
                $decoded = null;
            }
        }

        return is_array($decoded) ? $decoded : null;
    }

    private function load(ConfigProposalPolicyInterface $policy, string $name): ?Configuration
    {
        try {
            $configuration = Configuration::getByName($name);
        } catch (Throwable) {
            return null;
        }

        return $configuration?->getName() !== null && $configuration->getType() === $policy->configType()
            ? $configuration
            : null;
    }

    /**
     * The current user when they may configure the Data Hub at all. Never throws: an
     * unauthenticated user, an unresolvable one and a failing lookup are all a denial.
     */
    private function allowedUser(): ?User
    {
        try {
            $user = $this->securityService->getCurrentUser();
            $allowed = $user instanceof User && $user->isAllowed(PermissionConstants::PLUGIN_DATA_HUB_CONFIG);
        } catch (Throwable) {
            return null;
        }

        return $allowed && $user instanceof User ? $user : null;
    }

    private function denied(): CallToolResult
    {
        return $this->errorResult(
            sprintf('Missing permission %s', PermissionConstants::PLUGIN_DATA_HUB_CONFIG),
            self::CODE_PERMISSION_DENIED
        );
    }

    /**
     * The handler decides disclosure, this decides presentation — only an argument error comes
     * back verbatim, everything else becomes a generic sentence plus a correlation id.
     *
     * @param array<string, mixed> $context
     */
    private function handledError(Throwable $exception, string $tool, array $context = []): CallToolResult
    {
        return $this->errorResult(
            $this->errorHandler->handle($exception, $tool, $context),
            $exception instanceof InvalidMcpToolArgumentException
                ? self::CODE_INVALID_REQUEST
                : self::CODE_INTERNAL_ERROR,
        );
    }

    private function errorResult(string $message, string $code = self::CODE_INVALID_REQUEST): CallToolResult
    {
        return new CallToolResult(
            [new TextContent($this->encode(['error' => $message, 'code' => $code]))],
            isError: true
        );
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function successResult(array $payload): CallToolResult
    {
        return new CallToolResult([new TextContent($this->encode($payload))], isError: false);
    }

    /**
     * Compact on purpose: the payload is read by a model. JSON_INVALID_UTF8_SUBSTITUTE keeps a
     * label with invalid UTF-8 from collapsing the envelope to an empty result that reads as
     * success.
     *
     * @param array<string, mixed> $payload
     */
    private function encode(array $payload): string
    {
        return (string) json_encode($payload, JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
