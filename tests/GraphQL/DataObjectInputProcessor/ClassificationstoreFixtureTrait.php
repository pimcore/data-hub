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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\DataObjectInputProcessor;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor\Classificationstore as Processor;
use Pimcore\Bundle\DataHubBundle\Tests\GraphQL\ServiceStubTrait;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\Classificationstore as ClassificationstoreDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Layout\Panel;
use Pimcore\Model\DataObject\Classificationstore\GroupConfig;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;
use Pimcore\Model\DataObject\Classificationstore\KeyGroupRelation;
use Pimcore\Model\DataObject\Classificationstore\StoreConfig;
use Pimcore\Model\DataObject\Concrete;

/**
 * Builds a store with groups and keys, a class carrying it and an object, and runs the processor the way the
 * update resolver does (MutationType.php:884-933): save only when no processor threw, a thrown exception is a
 * success:false with its message.
 *
 * @internal
 */
trait ClassificationstoreFixtureTrait
{
    use ServiceStubTrait;

    private const FIELD = 'cs';

    private const READABLE_TYPES = ['input', 'numeric', 'checkbox', 'textarea', 'calculatedValue', 'multiValue'];

    /** @var string[]|null the types the read side publishes, the plain ones when null */
    private ?array $readable = null;

    private ?StoreConfig $store = null;

    /** @var GroupConfig[] */
    private array $groups = [];

    /** @var KeyConfig[] */
    private array $keys = [];

    /** @var KeyGroupRelation[] */
    private array $relations = [];

    /** @var Concrete[] */
    private array $objects = [];

    /** @var array<string, ClassDefinition> the classes built so far, by the allowed groups they carry */
    private array $classes = [];

    private ?ClassDefinition $class = null;

    protected function deleteFixtures(): void
    {
        foreach ($this->objects as $object) {
            $object->delete();
        }
        foreach ($this->relations as $relation) {
            $relation->getDao()->delete();
        }
        foreach ($this->keys as $key) {
            $key->delete();
        }
        foreach ($this->groups as $group) {
            $group->delete();
        }
        foreach ($this->classes as $class) {
            $class->delete();
        }
        $this->store?->delete();
        $this->objects = $this->relations = $this->keys = $this->groups = [];
        $this->classes = [];
        $this->class = $this->store = null;
    }

    private function createStore(): StoreConfig
    {
        $store = new StoreConfig();
        $store->setName('datahub-cs-write-' . uniqid());
        // qualified values (variants, positions) live in the document engine, the default EAV engine refuses them
        $store->setEngine('document');
        $store->save();

        return $store;
    }

    private function createGroup(string $identifier, ?StoreConfig $store = null): GroupConfig
    {
        $this->store ??= $this->createStore();
        $group = new GroupConfig();
        $group->setStoreId(($store ?? $this->store)->getId());
        $group->setName('datahub-cs-group-' . uniqid());
        $group->setIdentifier($identifier);
        $group->save();

        $this->groups[] = $group;

        return $group;
    }

    private function createKey(
        GroupConfig $group,
        string $identifier,
        string $type = 'input',
        array $definition = [],
        bool $enabled = true,
        bool $mandatory = false,
        ?bool $localized = null
    ): KeyGroupRelation {
        $definition += ['fieldtype' => $type, 'name' => $identifier];

        $key = new KeyConfig();
        $key->setStoreId($group->getStoreId());
        $key->setName('datahub-cs-key-' . $identifier);
        $key->setIdentifier($identifier);
        $key->setType($type);
        $key->setDefinition(json_encode($definition));
        // the flag lives on the key, a key that never decided about it is stored as disabled, and a relation
        // reads it from the key; the relation is created after the key, as saving a disabled key drops its relations
        $key->setEnabled($enabled);
        // null is a key that inherits the flag of the field it is rendered in
        $key->setLocalized($localized);
        $key->save();
        $this->keys[] = $key;

        $relation = KeyGroupRelation::create();
        $relation->setGroupId($group->getId());
        $relation->setKeyId($key->getId());
        $relation->setName($key->getName());
        $relation->setType($type);
        $relation->setDefinition(json_encode($definition));
        $relation->setEnabled($enabled);
        $relation->setMandatory($mandatory);
        $relation->save();

        $this->relations[] = $relation;

        return $relation;
    }

    /**
     * A multiValue key over an inner type, with a list and the variants "metric" and "imperial".
     */
    private function createMultiValueKey(
        GroupConfig $group,
        string $identifier,
        string $delegate = 'input'
    ): KeyGroupRelation {
        return $this->createKey($group, $identifier, 'multiValue', [
            'multiValue' => true,
            'variants' => [['name' => 'metric'], ['name' => 'imperial']],
            'delegateDatatype' => $delegate,
            'delegate' => ['fieldtype' => $delegate, 'name' => $identifier],
        ]);
    }

    private function createObject(array $allowedGroupIds = [], bool $localized = true): Concrete
    {
        $this->store ??= $this->createStore();

        $memo = implode(',', $allowedGroupIds) . ($localized ? '' : '|plain');
        $this->class = $this->classes[$memo] ?? null;
        if ($this->class === null) {
            $field = new ClassificationstoreDefinition();
            $field->setName(self::FIELD);
            $field->setTitle(self::FIELD);
            $field->setStoreId($this->store->getId());
            $field->setAllowedGroupIds($allowedGroupIds);
            $field->setLocalized($localized);

            $panel = new Panel();
            $panel->setChildren([$field]);

            $this->class = new ClassDefinition();
            $this->class->setName('DhCsWrite' . str_replace('.', '', uniqid('', true)));
            $this->class->setLayoutDefinitions($panel);
            $this->class->save();
            $this->classes[$memo] = $this->class;
        }

        $className = 'Pimcore\\Model\\DataObject\\' . $this->class->getName();
        /** @var Concrete $object */
        $object = new $className();
        $object->setParent(AbstractObject::getById(1));
        $object->setKey('cs-' . uniqid());
        $object->setPublished(true);
        $object->save();

        $this->objects[] = $object;

        return $object;
    }

    private function createProcessor(): Processor
    {
        $service = $this->createServiceStub();
        $service->setSupportedCsFeatureQueryDataTypes($this->readable ?? self::READABLE_TYPES);
        $processor = new Processor(['attributes' => ['attribute' => self::FIELD]]);
        $processor->setGraphQLService($service);

        return $processor;
    }

    /**
     * @return array{success: bool, message: string}
     */
    private function mutate(Concrete $object, array $input, bool $omitMandatoryCheck = false): array
    {
        $info = (new \ReflectionClass(ResolveInfo::class))->newInstanceWithoutConstructor();

        try {
            $object->setOmitMandatoryCheck($omitMandatoryCheck);
            $this->createProcessor()->process($object, $input, [], [], $info);
            $object->save();
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        return ['success' => true, 'message' => ''];
    }

    private function reload(Concrete $object): Concrete
    {
        return DataObject::getById($object->getId(), ['force' => true]);
    }

    private function store(Concrete $object): DataObject\Classificationstore
    {
        return $this->reload($object)->{'get' . ucfirst(self::FIELD)}();
    }

    /**
     * @return array{groups: array} the input writing the values of one key of one group, by numeric ids
     */
    private function input(GroupConfig $group, KeyGroupRelation $key, array $values, ?string $language = null): array
    {
        $groupInput = ['groupId' => $group->getId(), 'keys' => [['keyId' => $key->getKeyId(), 'values' => $values]]];
        if ($language !== null) {
            $groupInput['language'] = $language;
        }

        return ['groups' => [$groupInput]];
    }
}
