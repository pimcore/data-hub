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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\ClassificationstoreFeatureType;

use Codeception\Test\Unit;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\ElementResolver;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\ObjectsTargetType;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\Tests\GraphQL\ServiceStubTrait;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Data\ObjectMetadata;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Folder as ObjectFolder;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Folder as DocumentFolder;

/**
 * Every target of a Classification Store relation key goes through the read permission and is wrapped
 * in an ElementDescriptor (DataObjectQueryFieldConfigGenerator/Helper/Objects.php:73-81). The folder
 * read settings stand in for the workspace: the object folder is readable, the document folder is not.
 *
 * @internal
 */
class ElementResolverTest extends Unit
{
    use ServiceStubTrait;

    private const CLASS_A = 'ResolverClassA';

    private const CLASS_B = 'ResolverClassB';

    private mixed $backupContext = null;

    private array $backupDefinitions = [];

    protected function _before(): void
    {
        $this->extractDataCalls = [];
        $this->backupContext = RuntimeCache::isRegistered(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY)
            ? RuntimeCache::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY)
            : null;
        $this->backupDefinitions = ClassTypeDefinitions::$definitions;
        $this->resetObjectsTarget();
    }

    protected function _after(): void
    {
        if ($this->backupContext !== null) {
            RuntimeCache::set(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY, $this->backupContext);
        } else {
            RuntimeCache::getInstance()->offsetUnset(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        }
        ClassTypeDefinitions::$definitions = $this->backupDefinitions;
        $this->resetObjectsTarget();
    }

    public function testASingleTargetResolvesToItsDescriptor(): void
    {
        $this->useConfiguration([self::CLASS_A], ['object_folder' => true]);
        $folder = new ObjectFolder();

        $result = $this->resolver()->resolveElement($this->feature($folder));

        $this->assertInstanceOf(ElementDescriptor::class, $result);
        $this->assertCount(1, $this->extractDataCalls);
        $this->assertSame($result, $this->extractDataCalls[0][0]);
        $this->assertSame($folder, $this->extractDataCalls[0][1]);
    }

    public function testADeniedSingleTargetResolvesToNull(): void
    {
        $this->useConfiguration([self::CLASS_A], ['document_folder' => false]);

        $this->assertNull($this->resolver()->resolveElement($this->feature(new DocumentFolder())));
        $this->assertSame([], $this->extractDataCalls);
    }

    public function testADeniedTargetIsOmittedFromAList(): void
    {
        $this->useConfiguration([self::CLASS_A], ['object_folder' => true, 'document_folder' => false]);
        $allowed = new ObjectFolder();

        $result = $this->resolver()->resolveElements($this->feature([$allowed, new DocumentFolder()]));

        $this->assertCount(1, $result);
        $this->assertInstanceOf(ElementDescriptor::class, $result[0]);
        $this->assertSame($allowed, $this->extractDataCalls[0][1]);
    }

    public function testAnEmptyValueResolvesToNullForASingleTargetAndToNothingForAList(): void
    {
        $this->useConfiguration([self::CLASS_A], []);

        $this->assertNull($this->resolver()->resolveElement($this->feature(null)));
        $this->assertNull($this->resolver()->resolveElements($this->feature(null)));
        // the plain field helpers answer null for an empty value, not []
        $this->assertNull($this->resolver()->resolveElements($this->feature([])));
        $this->assertNull($this->resolver()->resolveAdvancedElements($this->feature([])));
        $this->assertNull($this->resolver()->resolveImageGallery($this->feature([])));
    }

    public function testAnObjectsOnlyKeyLeavesOutAnObjectFolder(): void
    {
        $this->useConfiguration([self::CLASS_A], ['object_folder' => true]);
        $folder = new ObjectFolder();
        $resolver = new ElementResolver($this->createServiceStub(), true);

        $this->assertSame([], $resolver->resolveElements($this->feature([$folder])));
        $this->assertSame([], $resolver->resolveAdvancedElements($this->feature([$this->metadata($folder, [])])));
    }

    public function testAnObjectsOnlyKeyLeavesOutAnObjectOfAClassThatIsNotAQueryEntity(): void
    {
        $this->useConfiguration([self::CLASS_A], []);
        ClassTypeDefinitions::$definitions = [
            self::CLASS_A => new ObjectType(['name' => self::CLASS_A, 'fields' => ['id' => Type::int()]]),
            self::CLASS_B => new ObjectType(['name' => self::CLASS_B, 'fields' => ['id' => Type::int()]]),
        ];
        $included = (new Concrete())->setClassName(self::CLASS_A);
        $excluded = (new Concrete())->setClassName(self::CLASS_B);
        $resolver = new ElementResolver($this->createServiceStub(), true);

        $result = $resolver->resolveElements($this->feature([$included, $excluded]));

        $this->assertCount(1, $result);
        $this->assertSame($included, $this->extractDataCalls[0][1]);
    }

    public function testAnAssetFolderFollowsTheAssetFolderSettingAndNotTheAssetSetting(): void
    {
        $this->useConfiguration([self::CLASS_A], ['asset' => true, 'asset_folder' => false]);
        $this->assertNull($this->resolver()->resolveElement($this->feature(new Asset\Folder())));

        $this->useConfiguration([self::CLASS_A], ['asset' => false, 'asset_folder' => true]);
        $this->assertNull($this->resolver()->resolveElement($this->feature(new Asset\Image())));
        $this->assertInstanceOf(
            ElementDescriptor::class,
            $this->resolver()->resolveElement($this->feature(new Asset\Folder()))
        );
    }

    public function testATargetOfATypeThatIsNotEnabledForQueryIsOmitted(): void
    {
        $this->useConfiguration([self::CLASS_A], ['asset' => false, 'document' => false]);

        $this->assertNull($this->resolver()->resolveElement($this->feature(new Asset\Image())));
        $this->assertNull($this->resolver()->resolveElement($this->feature(new Document\Page())));
        $this->assertSame([], $this->resolver()->resolveElements($this->feature([new Asset\Image()])));
    }

    public function testAnAdvancedRelationPublishesTheElementAndItsMetadataPairs(): void
    {
        $this->useConfiguration([self::CLASS_A], ['object_folder' => true, 'document_folder' => false]);
        $allowed = $this->metadata(new ObjectFolder(), ['one' => 'a', 'two' => 'b']);
        $denied = $this->metadata(new DocumentFolder(), ['one' => 'c']);

        $result = $this->resolver()->resolveAdvancedElements($this->feature([$allowed, $denied]));

        $this->assertCount(1, $result);
        $this->assertInstanceOf(ElementDescriptor::class, $result[0]['element']);
        $this->assertSame(
            [['name' => 'one', 'value' => 'a'], ['name' => 'two', 'value' => 'b']],
            $result[0]['metadata']
        );
    }

    public function testTheObjectTargetPublishesOnlyTheObjectClassesOfTheEndpoint(): void
    {
        $this->useConfiguration([self::CLASS_A], ['asset' => true, 'document' => true, 'object_folder' => true]);
        ClassTypeDefinitions::$definitions = [
            self::CLASS_A => new ObjectType(['name' => self::CLASS_A, 'fields' => ['id' => Type::int()]]),
            self::CLASS_B => new ObjectType(['name' => self::CLASS_B, 'fields' => ['id' => Type::int()]]),
        ];

        $names = array_map(
            static fn (ObjectType $type): string => $type->name,
            ObjectsTargetType::getInstance($this->createServiceStub())->getTypes()
        );

        $this->assertSame([self::CLASS_A], $names);
    }

    public function testTheObjectTargetFollowsTheConfigurationOfEachSchemaBuild(): void
    {
        ClassTypeDefinitions::$definitions = [
            self::CLASS_A => new ObjectType(['name' => self::CLASS_A, 'fields' => ['id' => Type::int()]]),
            self::CLASS_B => new ObjectType(['name' => self::CLASS_B, 'fields' => ['id' => Type::int()]]),
        ];
        $union = ObjectsTargetType::getInstance($this->createServiceStub());

        $this->useConfiguration([self::CLASS_A], []);
        $first = array_map(static fn (ObjectType $type): string => $type->name, $union->getTypes());

        $this->useConfiguration([self::CLASS_B], []);
        $second = array_map(static fn (ObjectType $type): string => $type->name, $union->getTypes());

        $this->assertSame([self::CLASS_A], $first);
        $this->assertSame([self::CLASS_B], $second);
        $this->assertSame($union, ObjectsTargetType::getInstance($this->createServiceStub()));
    }

    private function resolver(): ElementResolver
    {
        return new ElementResolver($this->createServiceStub());
    }

    private function feature(mixed $value): FeatureDescriptor
    {
        $descriptor = new FeatureDescriptor();
        $descriptor->setValue($value);

        return $descriptor;
    }

    private function metadata(ObjectFolder|DocumentFolder $element, array $data): ObjectMetadata
    {
        $metadata = $this->createMock(ObjectMetadata::class);
        $metadata->method('getElement')->willReturn($element);
        $metadata->method('getData')->willReturn($data);

        return $metadata;
    }

    /**
     * @param string[] $queryEntities
     * @param array<string, bool> $readSettings
     */
    private function useConfiguration(array $queryEntities, array $readSettings): void
    {
        $specialEntities = [];
        foreach ($readSettings as $entity => $enabled) {
            $specialEntities[$entity] = ['read' => $enabled];
        }

        $configuration = new Configuration('graphql', '/1', 'datahub-cs-feature-test');
        $configuration->setConfiguration([
            'schema' => [
                'queryEntities' => array_fill_keys($queryEntities, ['id' => 1]),
                'specialEntities' => $specialEntities,
            ],
            'security' => ['skipPermissionCheck' => true],
        ]);

        RuntimeCache::set(
            PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY,
            ['clientname' => 'datahub-cs-feature-test', 'configuration' => $configuration]
        );
    }

    private function resetObjectsTarget(): void
    {
        \Closure::bind(static function (): void {
            ObjectsTargetType::$instance = null;
        }, null, ObjectsTargetType::class)();
    }
}
