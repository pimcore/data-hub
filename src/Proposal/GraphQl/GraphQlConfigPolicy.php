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
use LogicException;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigProposalPolicyInterface;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use function sprintf;
use function strtolower;
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

    // the Studio save reads the generic types' flags under either name
    private const array WRITE_FLAGS = [
        'create' => 'createAllowed',
        'update' => 'updateAllowed',
        'delete' => 'deleteAllowed',
    ];

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
        $other = $this->otherType($state);
        if ($other !== null) {
            return [sprintf(
                'A %s configuration is already stored under this name; Data Hub names are shared by '
                . 'every adapter. Choose another name.',
                $other,
            )];
        }

        $problems = [...$this->misnamedEntities($state), ...$this->unknownClasses($state)];

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

    /**
     * The save keys an entity by its `id` and drops one without, so a key that is not its id
     * would be reviewed under one name and stored under another, or not at all.
     *
     * @param array<string, mixed> $state
     *
     * @return list<string>
     */
    private function misnamedEntities(array $state): array
    {
        $problems = [];
        foreach (['queryEntities', 'mutationEntities'] as $kind) {
            $entities = is_array($state['schema'][$kind] ?? null) ? $state['schema'][$kind] : [];
            foreach ($entities as $key => $entity) {
                if (!is_array($entity) || ($entity['id'] ?? null) !== (string) $key) {
                    $problems[] = sprintf(
                        'schema.%1$s.%2$s needs "id": "%2$s", the class name it is keyed by.',
                        $kind,
                        $key,
                    );
                }
            }
        }

        return $problems;
    }

    /**
     * @param array<string, mixed> $state
     *
     * @return list<string>
     */
    private function unknownClasses(array $state): array
    {
        $problems = [];
        foreach (['queryEntities', 'mutationEntities'] as $kind) {
            $entities = is_array($state['schema'][$kind] ?? null) ? $state['schema'][$kind] : [];
            foreach (array_keys($entities) as $key) {
                $class = (string) $key;
                if (ClassDefinition::getByName($class) === null) {
                    $problems[] = sprintf(
                        '"%s" is not the name of a data object class this installation has (schema.%s).',
                        $class,
                        $kind,
                    );
                }
            }
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
        // the subject handler keeps the stored type; a GraphQL document must not land on another adapter's
        $type = $configuration['general']['type'] ?? self::CONFIG_TYPE;
        if ($type !== self::CONFIG_TYPE) {
            throw new LogicException(sprintf(
                'Refusing to save a %s over the %s configuration "%s".',
                $this->noun(),
                is_string($type) ? $type : 'other',
                $name,
            ));
        }

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
        if (!is_array($entry)) {
            return [];
        }

        // any truthy value counts: the save casts these to bool, the mutation entities keep them as sent;
        // and under any case, as the workspace save calls set<Key>() and PHP method names ignore case
        $granted = [];
        foreach ($entry as $key => $value) {
            if ((bool) $value) {
                $granted[strtolower((string) $key)] = true;
            }
        }

        $flags = [];
        foreach (self::WRITE_FLAGS as $flag => $alias) {
            if (isset($granted[$flag]) || isset($granted[strtolower($alias)])) {
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
        $configuration = $this->find($state);

        return $configuration?->getType() === self::CONFIG_TYPE ? $configuration->getConfiguration() : [];
    }

    /**
     * The adapter type stored under the proposal's name when it is not GraphQL.
     *
     * @param array<string, mixed> $state
     */
    private function otherType(array $state): ?string
    {
        $type = $this->find($state)?->getType();

        return $type !== null && $type !== self::CONFIG_TYPE ? $type : null;
    }

    /**
     * @param array<string, mixed> $state
     */
    private function find(array $state): ?Configuration
    {
        $name = $state['general']['name'] ?? null;

        try {
            $configuration = is_string($name) ? Configuration::getByName($name) : null;
        } catch (Throwable) {
            return null;
        }

        return $configuration?->getName() !== null ? $configuration : null;
    }
}
