<?php declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType\QualifiedValueReader;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition\Data\Classificationstore as ClassificationstoreDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\Relations\AbstractRelations;
use Pimcore\Model\DataObject\ClassDefinition\Data\Video;
use Pimcore\Model\DataObject\Classificationstore as ClassificationstoreModel;
use Pimcore\Model\DataObject\Classificationstore\GroupConfig;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;
use Pimcore\Model\DataObject\Classificationstore\KeyGroupRelation;
use Pimcore\Model\DataObject\Classificationstore\Service as ClassificationstoreService;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\MultiValue;
use Pimcore\Tool;

/**
 * Writes the whole classification store of an object as one unit: every refusal is raised while the input is
 * planned, before the store is touched, and the resolver only saves the object when no processor threw.
 *
 * @internal
 */
final class Classificationstore extends Base
{
    private const DEFAULT_QUALIFIER = 'default';

    private const UNWRITABLE_TYPE = 'calculatedValue';

    /**
     * The key types a write accepts: the ones the read side publishes, whatever generators are registered.
     *
     * @return string[]
     */
    public function getAcceptedTypes(): array
    {
        $readable = $this->getGraphQlService()->getSupportedCsFeatureQueryDataTypes();

        return array_values(array_diff($readable, [self::UNWRITABLE_TYPE]));
    }

    /**
     * @param Concrete $object
     * @param mixed $newValue
     * @param array $args
     * @param array $context
     *
     * @throws \Exception
     */
    public function process($object, $newValue, $args, $context, ResolveInfo $info)
    {
        if ($newValue === null) {
            return;
        }

        $attribute = $this->getAttribute();
        $definition = $object instanceof Concrete ? $object->getClass()->getFieldDefinition($attribute) : null;
        if (!$definition instanceof ClassificationstoreDefinition) {
            throw new ClientSafeException(
                sprintf('"%s" is not a classification store field of the object.', $attribute)
            );
        }

        $getter = 'get' . ucfirst($attribute);
        $store = $object->$getter();

        $plan = $this->plan($store, $definition, $newValue);

        $this->activate($store, $plan['activate']);

        foreach ($plan['writes'] as $write) {
            $this->write($store, $write);
        }
    }

    /**
     * Activates the groups on the store itself:
     * with inherited values on, getActiveGroups() answers the ancestors' too.
     *
     * @param int[] $groupIds
     */
    private function activate(ClassificationstoreModel $store, array $groupIds): void
    {
        $this->ownValues(static function () use ($store, $groupIds): void {
            foreach ($groupIds as $groupId) {
                $store->setActiveGroups($store->getActiveGroups() + [$groupId => true]);
            }
        });
    }

    /**
     * Runs the callback with inherited values off, so every read in it answers what the object itself holds.
     */
    private function ownValues(callable $callback): mixed
    {
        $inherited = AbstractObject::getGetInheritedValues();
        AbstractObject::setGetInheritedValues(false);

        try {
            return $callback();
        } finally {
            AbstractObject::setGetInheritedValues($inherited);
        }
    }

    /**
     * @return array{activate: int[], writes: array<string, array>}
     *
     * @throws ClientSafeException
     */
    private function plan(
        ClassificationstoreModel $store,
        ClassificationstoreDefinition $definition,
        array $input
    ): array {
        $strict = ($input['strict'] ?? false) === true;
        $active = $this->ownValues(static fn (): array => $store->getActiveGroups());
        $activate = [];
        $writes = [];
        $slots = [];

        foreach ($input['groups'] ?? [] as $groupInput) {
            $group = $this->resolveGroup($groupInput, $definition);
            $groupId = $group->getId();
            $this->assertGroupWritable($group, $definition);

            if (($active[$groupId] ?? false) !== true) {
                if ($strict) {
                    throw new ClientSafeException(sprintf(
                        'The classification store group %s is inactive and "strict" is set.',
                        $this->describe($group)
                    ));
                }
                $activate[$groupId] = $groupId;
            }

            $language = $groupInput['language'] ?? self::DEFAULT_QUALIFIER;
            if ($language === '') {
                throw new ClientSafeException(sprintf(
                    'The language of the classification store group %s must not be empty.',
                    $this->describe($group)
                ));
            }
            foreach ($groupInput['keys'] ?? [] as $keyInput) {
                $relation = $this->resolveKey($keyInput, $group, $definition);
                $this->assertKeyWritable($relation, $group, $language, $definition);

                $writeId = implode("\0", [$groupId, $relation->getKeyId(), $language]);
                $writes[$writeId] ??= [
                    'groupId' => $groupId,
                    'keyId' => $relation->getKeyId(),
                    'language' => $language,
                    'entries' => [],
                ];

                foreach ($keyInput['values'] ?? [] as $valueInput) {
                    $entry = $this->planEntry($store, $relation, $valueInput, $slots, $language);
                    $writes[$writeId]['entries'][] = $entry;
                }
            }
        }

        return ['activate' => array_values($activate), 'writes' => $writes];
    }

    /**
     * @throws ClientSafeException
     */
    private function resolveGroup(array $input, ClassificationstoreDefinition $definition): GroupConfig
    {
        $id = $input['groupId'] ?? null;
        $identifier = $input['group'] ?? null;
        if (($id === null) === ($identifier === null)) {
            throw new ClientSafeException(
                'A classification store group is addressed by exactly one of "groupId" and "group".'
            );
        }

        $group = $id !== null
            ? GroupConfig::getById($id)
            : GroupConfig::getByIdentifier($identifier, $definition->getStoreId());
        if (!$group) {
            throw new ClientSafeException(
                sprintf('The classification store group "%s" does not resolve.', $id ?? $identifier)
            );
        }

        return $group;
    }

    /**
     * @throws ClientSafeException
     */
    private function assertGroupWritable(GroupConfig $group, ClassificationstoreDefinition $definition): void
    {
        if ($group->getStoreId() !== $definition->getStoreId()) {
            throw new ClientSafeException(sprintf(
                'The classification store group %s belongs to another store than the field "%s".',
                $this->describe($group),
                $definition->getName()
            ));
        }

        $allowed = array_map('intval', $definition->getAllowedGroupIds());
        if ($allowed !== [] && !in_array($group->getId(), $allowed, true)) {
            throw new ClientSafeException(sprintf(
                'The classification store group %s is not allowed on the field "%s".',
                $this->describe($group),
                $definition->getName()
            ));
        }
    }

    /**
     * @throws ClientSafeException
     */
    private function resolveKey(
        array $input,
        GroupConfig $group,
        ClassificationstoreDefinition $definition
    ): KeyGroupRelation {
        $id = $input['keyId'] ?? null;
        $identifier = $input['key'] ?? null;
        if (($id === null) === ($identifier === null)) {
            throw new ClientSafeException(
                'A classification store key is addressed by exactly one of "keyId" and "key".'
            );
        }

        $key = $id !== null
            ? KeyConfig::getById($id)
            : KeyConfig::getByIdentifier($identifier, $definition->getStoreId());
        if (!$key) {
            throw new ClientSafeException(
                sprintf('The classification store key "%s" does not resolve.', $id ?? $identifier)
            );
        }

        $relation = KeyGroupRelation::getByGroupAndKeyId($group->getId(), $key->getId());
        if (!$relation) {
            throw new ClientSafeException(sprintf(
                'The classification store key "%s" is not a key of the group %s.',
                $id ?? $identifier,
                $this->describe($group)
            ));
        }

        return $relation;
    }

    /**
     * @throws ClientSafeException
     */
    private function assertKeyWritable(
        KeyGroupRelation $relation,
        GroupConfig $group,
        string $language,
        ClassificationstoreDefinition $definition
    ): void {
        if (!$relation->isEnabled()) {
            throw new ClientSafeException(sprintf(
                'The classification store key "%s" is disabled in the group %s: '
                . 'a disabled key still publishes its stored values, and refuses new ones. '
                . 'Nothing was saved; remove this key from the mutation and send it again.',
                $relation->getName(),
                $this->describe($group)
            ));
        }

        if (!in_array(QualifiedValueReader::publishedType($relation), $this->getAcceptedTypes(), true)) {
            throw new ClientSafeException(sprintf(
                'The classification store key "%s" of the type "%s" cannot be written.',
                $relation->getName(),
                $relation->getType()
            ));
        }

        if ($language !== self::DEFAULT_QUALIFIER) {
            $localized = QualifiedValueReader::isKeyLocalized($relation->getKeyId(), $definition->isLocalized());
            if (!$localized || !in_array($language, Tool::getValidLanguages(), true)) {
                throw new ClientSafeException(sprintf(
                    'The classification store key "%s" in the group %s is not kept in the language "%s".',
                    $relation->getName(),
                    $this->describe($group),
                    $language
                ));
            }
        }
    }

    /**
     * @param array<string, bool> $slots the slots named so far, shared by the whole input
     *
     * @return array{variant: string, value: mixed}
     *
     * @throws ClientSafeException
     */
    private function planEntry(
        ClassificationstoreModel $store,
        KeyGroupRelation $relation,
        array $input,
        array &$slots,
        string $language
    ): array {
        if (!array_key_exists('value', $input)) {
            throw new ClientSafeException(sprintf(
                'A value of the classification store key "%s" carries no "value", use null to clear the slot.',
                $relation->getName()
            ));
        }

        $variant = $input['variant'] ?? self::DEFAULT_QUALIFIER;
        if ($variant === '') {
            throw new ClientSafeException(sprintf(
                'The variant of the classification store key "%s" must not be empty.',
                $relation->getName()
            ));
        }

        if (!in_array($variant, QualifiedValueReader::variants($relation), true)) {
            throw new ClientSafeException(sprintf(
                'The classification store key "%s" declares no variant "%s", its variants are: %s.',
                $relation->getName(),
                $variant,
                implode(', ', QualifiedValueReader::variants($relation))
            ));
        }

        $slot = implode("\0", [$relation->getGroupId(), $relation->getKeyId(), $language, $variant]);
        if (isset($slots[$slot])) {
            throw new ClientSafeException(sprintf(
                'Two values of the classification store key "%s" address the same slot '
                . '(language "%s", variant "%s").',
                $relation->getName(),
                $language,
                $variant
            ));
        }
        $slots[$slot] = true;

        if ($variant !== self::DEFAULT_QUALIFIER) {
            QualifiedValueReader::assertSupported($store);
        }

        $value = $input['value'];
        if ($value !== null) {
            $definition = ClassificationstoreService::getFieldDefinitionFromKeyConfig($relation);
            $converted = $definition->getDataFromEditmode(
                $this->withOptionalMembers($definition, $value),
                $store->getObject()
            );
            $this->assertRelationResolved($relation, $definition, $value, $converted);
            $value = $converted;
        }

        return ['variant' => $variant, 'value' => $value];
    }

    /**
     * Core's video conversion reads "poster" without a default, so a value that leaves it out raises "Undefined array
     * key". The plain-field input accepts such a value, so the member is filled with null here.
     */
    private function withOptionalMembers(mixed $definition, mixed $value): mixed
    {
        if ($definition instanceof Video && is_array($value)) {
            $value['poster'] ??= null;
        }

        return $value;
    }

    /**
     * The editmode conversion drops a relation target it cannot find. A write that names one must not clear the
     * slot or store a shorter list than it was given; an explicit null is the way to clear.
     *
     * @throws ClientSafeException
     */
    private function assertRelationResolved(
        KeyGroupRelation $relation,
        mixed $definition,
        mixed $input,
        mixed $converted
    ): void {
        if (!$definition instanceof AbstractRelations || !is_array($input)) {
            return;
        }

        // A list is one target per entry; anything else is a single descriptor map.
        $expected = array_is_list($input) ? count($input) : 1;

        if (is_array($converted)) {
            $actual = count($converted);
        } else {
            $actual = $converted === null ? 0 : 1;
        }

        if ($input !== [] && $actual !== $expected) {
            throw new ClientSafeException(sprintf(
                'A target of the classification store key "%s" does not exist or is not of a type the key accepts. '
                . 'Nothing was saved.',
                $relation->getName()
            ));
        }
    }

    /**
     * Writes the named slots and leaves every other slot of the key as it is.
     */
    private function write(ClassificationstoreModel $store, array $write): void
    {
        $qualified = [];
        foreach ($write['entries'] as $entry) {
            if ($entry['variant'] === self::DEFAULT_QUALIFIER) {
                $store->setLocalizedKeyValue($write['groupId'], $write['keyId'], $entry['value'], $write['language']);
            } else {
                $qualified[$entry['variant']] = $entry['value'];
            }
        }

        if ($qualified !== []) {
            $store->setKeyValues($write['groupId'], $write['keyId'], $this->slotEntries($write, $qualified));
        }
    }

    /**
     * setKeyValues() replaces each (language, variant) slot it is given as a whole and leaves every
     * other slot of the key alone, so only the variants the input names are handed over, one value
     * each. A value stored above position 0 needs no carrying over here: no write can address one,
     * and the store puts back what it holds there on the next save.
     *
     * @param array<string|int, mixed> $qualified the named values by variant
     *
     * @return MultiValue[]
     */
    private function slotEntries(array $write, array $qualified): array
    {
        $entries = [];
        foreach ($qualified as $variant => $named) {
            $entries[] = new MultiValue($named, $write['language'], (string) $variant);
        }

        return $entries;
    }

    private function describe(GroupConfig $group): string
    {
        return sprintf('"%s" (%d)', $group->getName(), $group->getId());
    }
}
