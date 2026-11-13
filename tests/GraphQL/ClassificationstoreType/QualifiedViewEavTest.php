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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\ClassificationstoreType;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType\Feature;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType\Group;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\Tests\GraphQL\DataObjectInputProcessor\ClassificationstoreFixtureTrait;
use Pimcore\Model\AbstractModel;
use Pimcore\Model\Dao\DaoInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Layout\Panel;
use Pimcore\Model\DataObject\Classificationstore;

/**
 * @internal
 */
class QualifiedViewEavTest extends Unit
{
    use ClassificationstoreFixtureTrait;

    private function createEavStore(): Classificationstore
    {
        $dao = new class() implements DaoInterface {
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
                return false;
            }
        };

        $store = $this->getMockBuilder(Classificationstore::class)
            ->onlyMethods([
                'getDao',
                'getClass',
                'getFieldname',
                'getLocalizedKeyValue',
                'setLocalizedKeyValue',
                'getKeyValues',
            ])
            ->getMock();
        $store->method('getDao')->willReturn($dao);
        $store->method('getClass')->willReturn(new ClassDefinition());
        $store->method('getFieldname')->willReturn('csfield');
        $store->method('getLocalizedKeyValue')->willReturn('plain');
        $store->expects($this->never())->method('getKeyValues');

        return $store;
    }

    public function testTheQualifiedViewIsRefusedNamingTheStoreWhileFeaturesKeepWorking(): void
    {
        $group = $this->createGroup('eav_group');
        $this->createKey($group, 'k');
        $service = $this->createServiceStub();
        $service->setSupportedCsFeatureQueryDataTypes(self::READABLE_TYPES);
        $type = new Group($service, new Feature($service));
        $value = ['id' => $group->getId(), '_csValue' => $this->createEavStore(), '_language' => 'default'];

        $features = $type->getField('features')->resolveFn;
        $this->assertSame(['plain'], array_map(static fn ($f) => $f->getValue(), $features($value)));

        $this->expectException(ClientSafeException::class);
        $this->expectExceptionMessage('csfield');
        $qualified = $type->getField('qualifiedFeatures')->resolveFn;
        $qualified($value);
    }

    public function testAQualifiedWriteIsRefusedAndAnUnqualifiedOneIsNot(): void
    {
        $group = $this->createGroup('eav_write_group');
        $key = $this->createKey($group, 'k');
        $definition = new \Pimcore\Model\DataObject\ClassDefinition\Data\Classificationstore();
        $definition->setStoreId($group->getStoreId());
        $definition->setName(self::FIELD);
        $panel = new Panel();
        $panel->setChildren([$definition]);
        $class = new ClassDefinition();
        $class->setLayoutDefinitions($panel);
        $store = $this->createEavStore();
        $store->expects($this->once())->method('setLocalizedKeyValue');
        $object = new class($class, $store) extends \Pimcore\Model\DataObject\Concrete {
            public function __construct(
                private readonly ClassDefinition $stubClass,
                private readonly Classificationstore $stubStore
            ) {
            }

            public function getClass(): ClassDefinition
            {
                return $this->stubClass;
            }

            public function getCs(): Classificationstore
            {
                return $this->stubStore;
            }
        };

        $unqualified = $this->mutateWithoutSave($object, $this->input($group, $key, [['value' => 'x']]));
        $entries = [['value' => 'x', 'variant' => 'imperial']];
        $qualified = $this->mutateWithoutSave($object, $this->input($group, $key, $entries));

        $this->assertTrue($unqualified['success'], $unqualified['message']);
        $this->assertFalse($qualified['success']);
        $this->assertStringContainsString('csfield', $qualified['message']);
    }

    /**
     * @return array{success: bool, message: string}
     */
    private function mutateWithoutSave(\Pimcore\Model\DataObject\Concrete $object, array $input): array
    {
        $info = (new \ReflectionClass(\GraphQL\Type\Definition\ResolveInfo::class))->newInstanceWithoutConstructor();
        try {
            $this->createProcessor()->process($object, $input, [], [], $info);
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        return ['success' => true, 'message' => ''];
    }
}
