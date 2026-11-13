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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\ClassificationstoreFeatureQueryTypeGenerator;

use Codeception\Test\Unit;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureQueryTypeGenerator\Checkbox;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureQueryTypeGenerator\Input;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureQueryTypeGenerator\MultiValue;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\MultiValueType;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType\Feature;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType\QualifiedValueReader;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\Tests\GraphQL\ServiceStubTrait;
use Pimcore\Model\AbstractModel;
use Pimcore\Model\Dao\DaoInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Classificationstore;
use Pimcore\Model\DataObject\Classificationstore\KeyGroupRelation;
use Psr\Container\ContainerInterface;

/**
 * @internal
 */
class MultiValueTest extends Unit
{
    use ServiceStubTrait;

    private const GROUP_ID = 7;

    private const KEY_ID = 8;

    private function createCsService(): Service
    {
        $service = $this->createServiceStub();
        $generators = new class($service) implements ContainerInterface {
            public function __construct(private readonly Service $service)
            {
            }

            public function get(string $id): mixed
            {
                return match ($id) {
                    'typegenerator_csfeaturequerydatatype_multiValue' => new MultiValue($this->service),
                    'typegenerator_csfeaturequerydatatype_checkbox' => new Checkbox($this->service),
                    default => new Input($this->service),
                };
            }

            public function has(string $id): bool
            {
                return true;
            }
        };

        \Closure::bind(static function (Service $service, object $generators): void {
            $service->csFeatureTypeGeneratorFactories = $generators;
        }, null, Service::class)($service, $generators);

        $service->setSupportedCsFeatureQueryDataTypes(['input', 'checkbox', 'multiValue']);
        $service->registerClassificationStoreDataTypes(['cs_feature' => new Feature($service)]);

        return $service;
    }

    private function createDescriptor(
        Classificationstore $store,
        string $delegate,
        int $keyId = self::KEY_ID
    ): FeatureDescriptor {
        $relation = KeyGroupRelation::create();
        $relation->setGroupId(self::GROUP_ID);
        $relation->setKeyId($keyId);
        $relation->setType('multiValue');
        $relation->setDefinition(json_encode([
            'fieldtype' => 'multiValue',
            'name' => 'weight',
            'multiValue' => true,
            'delegateDatatype' => $delegate,
            'delegate' => ['fieldtype' => $delegate, 'name' => 'weight'],
        ]));

        $descriptor = new FeatureDescriptor();
        $descriptor->setId($keyId);
        $descriptor->setType('multiValue');
        $descriptor->setStore($store);
        $descriptor->setGroupId(self::GROUP_ID);
        $descriptor->setLanguage('default');
        $descriptor->setRelation($relation);

        return $descriptor;
    }

    private function createStore(bool $qualified): Classificationstore
    {
        $dao = new class($qualified) implements DaoInterface {
            public function __construct(private readonly bool $qualified)
            {
            }

            public function setModel(AbstractModel $model): static
            {
                return $this;
            }

            public function configure(): void
            {
                // Nothing to configure: this stub DAO only answers supports().
            }

            public function supports(): bool
            {
                return $this->qualified;
            }
        };

        $store = $this->getMockBuilder(Classificationstore::class)
            ->onlyMethods(['getDao', 'getClass', 'getLocalizedKeyValue', 'getKeyValues'])
            ->getMock();
        $store->method('getDao')->willReturn($dao);
        $store->method('getClass')->willReturn(new ClassDefinition());

        return $store;
    }

    public function testMultiValueIsAMemberOfTheFeatureUnionAndTheSchemaIsValid(): void
    {
        $service = $this->createCsService();
        $union = $service->getClassificationStoreTypeDefinition('cs_feature');

        $names = array_map(static fn (Type $type) => $type->name, $union->getTypes());
        $this->assertContains('csFeatureMultiValue', $names);

        $schema = new Schema([
            'query' => new ObjectType([
                'name' => 'Query',
                'fields' => ['features' => ['type' => Type::listOf($union)]],
            ]),
        ]);
        $schema->assertValid();
    }

    public function testTheValueMemberResolvesThroughTheDeclaredDelegate(): void
    {
        $service = $this->createCsService();
        $union = $service->getClassificationStoreTypeDefinition('cs_feature');

        $keyId = self::KEY_ID + 100;
        foreach (['input' => 'csFeatureInput', 'checkbox' => 'csFeatureCheckbox'] as $delegate => $typeName) {
            $store = $this->createStore(true);
            $store->method('getKeyValues')->willReturn([0 => 'v']);
            // the definition of a relation is memoized by its group and key id, one key per delegate
            $entries = QualifiedValueReader::entries($this->createDescriptor($store, $delegate, ++$keyId));

            $this->assertNotEmpty($entries);
            $info = (new \ReflectionClass(\GraphQL\Type\Definition\ResolveInfo::class))
                ->newInstanceWithoutConstructor();
            $resolved = $union->resolveType($entries[0]['value'], [], $info);
            $this->assertSame($typeName, $resolved->name);
        }

        $this->assertSame('csFeatureMultiValue', MultiValueType::getInstance($service)->name);
    }

    public function testMultiValueDegradesOnAnEavStore(): void
    {
        $store = $this->createStore(false);
        $store->expects($this->once())
            ->method('getLocalizedKeyValue')
            ->with(self::GROUP_ID, self::KEY_ID, 'default')
            ->willReturn('plain');
        $store->expects($this->never())->method('getKeyValues');

        $entries = QualifiedValueReader::entries($this->createDescriptor($store, 'input'));

        $this->assertCount(1, $entries);
        $this->assertNull($entries[0]['language']);
        $this->assertNull($entries[0]['variant']);
        $this->assertNull($entries[0]['position']);
        $this->assertSame('plain', $entries[0]['value']->getValue());
    }
}
