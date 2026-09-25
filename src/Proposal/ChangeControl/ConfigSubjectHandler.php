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

namespace Pimcore\Bundle\DataHubBundle\Proposal\ChangeControl;

use LogicException;
use Pimcore\Bundle\ChangeControlBundle\Merge\Exception\UnsupportedMergeSaveModeException;
use Pimcore\Bundle\ChangeControlBundle\Merge\MergeSaveMode;
use Pimcore\Bundle\ChangeControlBundle\Merge\StateShapeInterface;
use Pimcore\Bundle\ChangeControlBundle\Subject\Exception\SubjectAccessDeniedException;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectHandlerInterface;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectRef;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigDocument;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigProposalPolicyInterface;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Pimcore\Bundle\DataHubBundle\Utils\Constants\PermissionConstants;
use Pimcore\Model\User;
use Pimcore\Model\UserInterface;
use function sprintf;
use Throwable;

/**
 * One adapter's configurations as a Change Control subject, keyed by configuration name.
 * Registered once per adapter, each with its own policy.
 */
final readonly class ConfigSubjectHandler implements SubjectHandlerInterface
{
    public function __construct(
        private ConfigProposalPolicyInterface $policy,
        private ConfigurationServiceInterface $configurations,
    ) {
    }

    public function outOfDraftScope(): array
    {
        return ConfigDocument::withheldSections($this->policy);
    }

    public function shape(SubjectRef $subject): StateShapeInterface
    {
        return new ConfigStateShape();
    }

    public function readState(SubjectRef $subject): array
    {
        $configuration = $this->load($subject->ref);

        // no configuration yet: the create lane has no base and merges last-write-wins
        return $configuration === null
            ? []
            : ConfigDocument::readable($configuration->getConfiguration(), $this->policy);
    }

    /**
     * Reviewing shows the configuration's current values, so it is gated exactly like opening
     * it in the Data Hub.
     *
     * @throws SubjectAccessDeniedException
     */
    public function authorizeRead(SubjectRef $subject, UserInterface $user): void
    {
        $this->assertConfigPermission($subject, $user, PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_READ);
    }

    public function authorize(SubjectRef $subject, UserInterface $user, MergeSaveMode $mode): void
    {
        // a configuration is live the moment it is written, so a mode this cannot land is
        // refused here rather than at apply — the review meta probes with each mode
        if ($mode !== MergeSaveMode::Publish) {
            throw SubjectAccessDeniedException::forRef(
                $this->policy->subjectType(),
                $subject->ref,
                MergeSaveMode::Publish->requiredPermission(),
            );
        }

        $this->assertConfigPermission($subject, $user, PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_UPDATE);
    }

    public function applyMerged(
        SubjectRef $subject,
        array $finalData,
        UserInterface $mergedBy,
        MergeSaveMode $mode,
    ): array {
        if ($mode !== MergeSaveMode::Publish) {
            throw UnsupportedMergeSaveModeException::by(self::class, $mode);
        }

        $name = $subject->ref;

        if ($finalData === []) {
            // an empty merged state would be a removal; a configuration is never deleted through a merge
            throw new LogicException(
                sprintf('Refusing to apply an empty state to %s "%s".', $this->policy->noun(), $name)
            );
        }

        $existing = $this->load($name);
        if ($existing === null) {
            $this->configurations->addConfiguration(
                $name,
                $this->policy->configType(),
                (string) ($finalData['general']['path'] ?? '')
            );
            $existing = $this->load($name);
        }

        $configuration = ConfigDocument::withStored($finalData, $existing?->getConfiguration() ?? [], $this->policy);

        // identity and adapter type are the subject's, not the draft's — an existing
        // configuration keeps whatever type it was created with
        $configuration['general']['name'] = $name;
        $configuration['general']['type'] = $existing?->getType() ?? $this->policy->configType();

        $this->policy->save($name, $configuration);

        return [];
    }

    private function load(string $name): ?Configuration
    {
        try {
            $configuration = Configuration::getByName($name);
        } catch (Throwable) {
            return null;
        }

        return $configuration?->getName() !== null ? $configuration : null;
    }

    /**
     * @throws SubjectAccessDeniedException
     */
    private function assertConfigPermission(SubjectRef $subject, UserInterface $user, string $permission): void
    {
        // isAllowed() wants the concrete user model; an actor this cannot verify is denied
        $configuration = $this->load($subject->ref);
        $allowed = $user instanceof User
            && $user->isAllowed(PermissionConstants::PLUGIN_DATA_HUB_CONFIG)
            // a configuration that does not exist yet is gated by the area permission alone
            && ($configuration === null || $configuration->isAllowed($permission, $user));

        if (!$allowed) {
            throw SubjectAccessDeniedException::forRef(
                $this->policy->subjectType(),
                $subject->ref,
                PermissionConstants::PLUGIN_DATA_HUB_CONFIG
            );
        }
    }
}
