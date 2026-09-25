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

namespace Pimcore\Bundle\DataHubBundle\Proposal\GraphQl;

use function array_diff;
use function array_keys;
use function array_values;
use function count;
use function implode;
use function is_array;
use function is_string;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigProposalPolicyInterface;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use function sprintf;
use Throwable;
use function time;

/**
 * What an agent may propose about a GraphQL endpoint: which classes and fields it exposes and
 * which trees it may read — never who may call it, and never write access.
 *
 * Refused outright, each for its own reason:
 *
 * - `security` holds the API keys, the authentication method and the switch that skips the
 *   workspace check altogether.
 * - `permissions` is the ACL the Data Hub reads to decide who may change a configuration, so a
 *   proposal carrying it could propose who may approve the next one.
 * - write access the endpoint does not already grant — create, update or delete on a workspace,
 *   a mutation entity or a generic type. Reading is reviewed; writing through the API is
 *   granted by a person.
 *
 * @internal
 */
final readonly class GraphQlConfigPolicy implements ConfigProposalPolicyInterface
{
    public const string CONFIG_TYPE = 'graphql';

    public const string SUBJECT_TYPE = 'data-hub-graphql-config';

    public const array PROPOSABLE = ['general', 'schema', 'workspaces'];

    public const array WITHHELD = ['security', 'permissions'];

    private const array WRITE_FLAGS = ['create', 'update', 'delete'];

    public function __construct(private ConfigurationServiceInterface $configurations)
    {
    }

    public function configType(): string
    {
        return self::CONFIG_TYPE;
    }

    public function subjectType(): string
    {
        return self::SUBJECT_TYPE;
    }

    public function noun(): string
    {
        return 'GraphQL configuration';
    }

    public function readToolName(): string
    {
        return 'get_graphql_config';
    }

    public function proposable(): array
    {
        return self::PROPOSABLE;
    }

    public function withheld(): array
    {
        return self::WITHHELD;
    }

    public function withheldReason(): string
    {
        return 'The security settings hold the API keys, the authentication method and the switch '
            . 'that skips the workspace check, and permissions decide who may approve a proposal — '
            . 'they are changed by a person in the Data Hub editor.';
    }

    public function slots(): array
    {
        // the editor keeps workspaces on its security tab
        return [
            'general' => ['general'],
            'schema' => ['schema'],
            'security' => ['workspaces'],
        ];
    }

    public function problems(array $state, bool $isNew): array
    {
        $problems = [];

        foreach (['queryEntities', 'mutationEntities'] as $kind) {
            $entities = $state['schema'][$kind] ?? [];
            foreach (is_array($entities) ? $entities : [] as $key => $entity) {
                $class = is_array($entity) && is_string($entity['id'] ?? null) ? $entity['id'] : (string) $key;
                if (ClassDefinition::getByName($class) === null) {
                    $problems[] = sprintf(
                        '"%s" is not the name of a data object class this installation has (schema.%s).',
                        $class,
                        $kind,
                    );
                }
            }
        }

        $granted = array_values(array_diff($this->writeGrants($state), $this->writeGrants($this->stored($state))));
        if ($granted !== []) {
            $problems[] = sprintf(
                'Write access cannot be proposed (%s). Propose read access only; a person grants '
                . 'create, update and delete in the Data Hub editor.',
                implode(', ', $granted),
            );
        }

        $queries = $state['schema']['queryEntities'] ?? [];
        if ($isNew && (!is_array($queries) || $queries === [])) {
            $problems[] = 'A new configuration needs at least one query entity (schema.queryEntities). '
                . 'Read a similar one with get_graphql_config and send the complete document under '
                . 'the new name.';
        }

        return $problems;
    }

    public function summarize(array $configuration): array
    {
        $queries = $configuration['schema']['queryEntities'] ?? [];
        $mutations = $configuration['schema']['mutationEntities'] ?? [];

        return [
            'queryEntities' => is_array($queries) ? array_keys($queries) : [],
            'mutationEntityCount' => is_array($mutations) ? count($mutations) : 0,
        ];
    }

    public function save(string $name, array $configuration): void
    {
        // the Studio save dehydrates what the editor sends, which lists the generic types by
        // name; storage keys them, and a keyed entry would be dropped on the way in
        $special = $configuration['schema']['specialEntities'] ?? null;
        if (is_array($special)) {
            $configuration['schema']['specialEntities'] = [];
            foreach ($special as $type => $flags) {
                $configuration['schema']['specialEntities'][] = [
                    'name' => (string) $type,
                    'readAllowed' => (bool) ($flags['read'] ?? $flags['readAllowed'] ?? false),
                    'createAllowed' => (bool) ($flags['create'] ?? $flags['createAllowed'] ?? false),
                    'updateAllowed' => (bool) ($flags['update'] ?? $flags['updateAllowed'] ?? false),
                    'deleteAllowed' => (bool) ($flags['delete'] ?? $flags['deleteAllowed'] ?? false),
                ];
            }
        }

        // the stored modification date is the one the merge just read; passing now defeats the
        // editor's stale-write guard, which a reviewed merge has already answered
        $this->configurations->updateConfiguration($name, $configuration, time());
    }

    /**
     * Every write permission a document grants, as a readable address.
     *
     * @param array<string, mixed> $document
     *
     * @return list<string>
     */
    private function writeGrants(array $document): array
    {
        $grants = [];

        $workspaces = is_array($document['workspaces'] ?? null) ? $document['workspaces'] : [];
        foreach ($workspaces as $kind => $entries) {
            foreach (is_array($entries) ? $entries : [] as $entry) {
                $path = is_array($entry) && is_string($entry['cpath'] ?? null) ? $entry['cpath'] : '';
                $grants = [...$grants, ...$this->flagsOf($entry, sprintf('workspaces.%s %s', $kind, $path))];
            }
        }

        foreach (['mutationEntities', 'specialEntities'] as $kind) {
            $entities = is_array($document['schema'][$kind] ?? null) ? $document['schema'][$kind] : [];
            foreach ($entities as $key => $entity) {
                $grants = [...$grants, ...$this->flagsOf($entity, sprintf('schema.%s.%s', $kind, $key))];
            }
        }

        return $grants;
    }

    /**
     * @return list<string>
     */
    private function flagsOf(mixed $entry, string $address): array
    {
        $flags = [];
        foreach (self::WRITE_FLAGS as $flag) {
            if (is_array($entry) && ($entry[$flag] ?? false) === true) {
                $flags[] = $address . ' ' . $flag;
            }
        }

        return $flags;
    }

    /**
     * The configuration as stored, so a grant it already has is not taken for a proposed one.
     *
     * @param array<string, mixed> $state
     *
     * @return array<string, mixed>
     */
    private function stored(array $state): array
    {
        $name = $state['general']['name'] ?? null;

        try {
            $configuration = is_string($name) ? Configuration::getByName($name) : null;
        } catch (Throwable) {
            $configuration = null;
        }

        return $configuration?->getType() === self::CONFIG_TYPE ? $configuration->getConfiguration() : [];
    }
}
