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
use function array_map;
use function array_values;
use function count;
use function implode;
use function in_array;
use function is_array;
use function is_int;
use function is_string;
use LogicException;
use Pimcore\Bundle\DataHubBundle\ConfigEvents;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Event\Config\SpecialEntitiesEvent;
use Pimcore\Bundle\DataHubBundle\Model\SpecialEntitySetting;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigProposalPolicyInterface;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use function sprintf;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
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

    // the generic types the Studio editor offers before listeners add their own
    private const array GENERIC_TYPES = [
        'document',
        'document_folder',
        'asset',
        'asset_folder',
        'asset_listing',
        'object_folder',
        'translation',
        'translation_listing',
    ];

    public function __construct(
        private ConfigurationServiceInterface $configurations,
        private EventDispatcherInterface $events,
    ) {
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

        $problems = [
            ...$this->misnamedEntities($state),
            ...$this->misshapenGenericTypes($state),
            ...$this->unknownClasses($state),
            ...(new GraphQlWorkspacePaths())->unresolved($state),
        ];

        $grants = new GraphQlWriteGrants();
        $granted = array_values(array_diff($grants->of($state), $grants->of($this->stored($state))));
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
     * Storage keys the generic types by name and drops a name the editor does not offer, so a
     * list, a bare flag or an unknown name would be reviewed as access and stored as none.
     *
     * @param array<string, mixed> $state
     *
     * @return list<string>
     */
    private function misshapenGenericTypes(array $state): array
    {
        $special = $state['schema']['specialEntities'] ?? [];
        if (!is_array($special)) {
            return ['schema.specialEntities needs the generic types keyed by name, e.g. {"asset": {"read": true}}.'];
        }

        $problems = [];
        $known = null;
        foreach ($special as $type => $flags) {
            if (is_int($type) || !is_array($flags)) {
                $problems[] = sprintf(
                    'schema.specialEntities.%s needs the generic type\'s name as its key and its flags as '
                    . 'an object, e.g. {"asset": {"read": true}}.',
                    $type,
                );

                continue;
            }

            $known ??= $this->genericTypes($state);
            if (!in_array($type, $known, true)) {
                $problems[] = sprintf(
                    'schema.specialEntities.%s is not a generic type; use %s.',
                    $type,
                    implode(', ', $known),
                );
            }
        }

        return $problems;
    }

    /**
     * @param array<string, mixed> $state
     *
     * @return list<string>
     */
    private function genericTypes(array $state): array
    {
        $core = array_map(
            static fn (string $name): SpecialEntitySetting => new SpecialEntitySetting(
                $name,
                true,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
            ),
            self::GENERIC_TYPES,
        );
        $event = new SpecialEntitiesEvent($core, $state);
        $this->events->dispatch($event, ConfigEvents::SPECIAL_ENTITIES);

        return array_values(array_map(
            static fn (SpecialEntitySetting $setting): string => (string) $setting->getName(),
            $event->getSpecialSettings(),
        ));
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
                // an entry already in the editor's form names itself; a list index is no type
                $name = is_array($flags) && is_int($type) ? ($flags['name'] ?? null) : $type;
                if (!is_array($flags) || !is_string($name) || $name === '') {
                    continue;
                }

                $configuration['schema']['specialEntities'][] = [
                    'name' => $name,
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
