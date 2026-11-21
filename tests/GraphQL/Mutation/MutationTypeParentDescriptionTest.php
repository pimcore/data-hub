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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\Mutation;

use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Cache;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Db;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Tests\Support\Util\TestHelper;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * The "not allowed to create ..." messages name the parent the caller asked for,
 * so a wrong value passed through indirection is easy to spot.
 */
class MutationTypeParentDescriptionTest extends Unit
{
    private const CONFIG_NAME = 'datahub-parent-description-test';

    private const PREFIX = 'datahub_parent_description_';

    private mixed $backupContext = null;

    private bool $hadContext = false;

    private ?ElementInterface $parent = null;

    protected function setUp(): void
    {
        // the workspace lookup behind the permission check needs the bundle's tables, which the CI database lacks
        foreach (['document', 'asset', 'object'] as $type) {
            Db::get()->executeStatement(
                'CREATE TABLE IF NOT EXISTS ' . Db::get()->quoteIdentifier('plugin_datahub_workspaces_' . $type) . ' (
                    `cid` INT(11) UNSIGNED NOT NULL DEFAULT 0,
                    `cpath` VARCHAR(765) NULL DEFAULT NULL,
                    `configuration` VARCHAR(80) NOT NULL DEFAULT \'0\',
                    `create` TINYINT(1) UNSIGNED NULL DEFAULT 0,
                    `read` TINYINT(1) UNSIGNED NULL DEFAULT 0,
                    `update` TINYINT(1) UNSIGNED NULL DEFAULT 0,
                    `delete` TINYINT(1) UNSIGNED NULL DEFAULT 0,
                    PRIMARY KEY (`cid`, `configuration`)
                )'
            );
        }

        $this->hadContext = RuntimeCache::isRegistered(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        if ($this->hadContext) {
            $this->backupContext = RuntimeCache::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        }

        // a configuration without workspace entries denies every create
        $configuration = new Configuration('graphql', '/1', self::CONFIG_NAME);
        $configuration->setConfiguration([]);
        RuntimeCache::set(
            PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY,
            ['clientname' => self::CONFIG_NAME, 'configuration' => $configuration]
        );
    }

    protected function tearDown(): void
    {
        if ($this->hadContext) {
            RuntimeCache::set(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY, $this->backupContext);
        } elseif (RuntimeCache::isRegistered(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY)) {
            RuntimeCache::getInstance()->offsetUnset(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        }

        // the permission check stores the workspace lookup under these keys
        foreach (['document', 'asset', 'object'] as $type) {
            $key = 'datahub_workspace_permissions_' . self::CONFIG_NAME . '_' . $type;
            Cache::remove($key);
            if (RuntimeCache::isRegistered($key)) {
                RuntimeCache::getInstance()->offsetUnset($key);
            }
        }

        $this->parent?->delete();
        $this->parent = null;
    }

    public static function parentArgs(): array
    {
        return [
            'parent id' => [['parentId' => 5], ' in parent id 5'],
            'path' => [['path' => '/News/2026'], " in parent path '/News/2026'"],
            'parent id wins over path' => [['parentId' => 5, 'path' => '/News'], ' in parent id 5'],
            'neither' => [[], ''],
        ];
    }

    #[DataProvider('parentArgs')]
    public function testDescribeParent(array $args, string $expected): void
    {
        $method = new \ReflectionMethod(MutationType::class, 'describeParent');

        $this->assertSame($expected, $method->invoke(null, $args));
    }

    public static function elementTypes(): array
    {
        return [
            'asset' => ['asset', 'assetfolder'],
            'object' => ['object', 'objectfolder'],
            'document' => ['document', 'documentfolder'],
        ];
    }

    /**
     * Guards that the folder resolver appends the parent to its message, not only that the helper works.
     */
    #[DataProvider('elementTypes')]
    public function testCreateFolderDeniedMessageNamesParentIdAndPath(string $elementType, string $folderType): void
    {
        $this->parent = match ($elementType) {
            'asset' => TestHelper::createAssetFolder(self::PREFIX),
            'object' => TestHelper::createObjectFolder(self::PREFIX),
            'document' => TestHelper::createDocumentFolder(self::PREFIX),
        };

        $resolver = $this->createFolderResolver($elementType);

        $info = $this->createStub(ResolveInfo::class);
        $parentId = $this->parent->getId();

        $byId = $resolver(null, ['parentId' => $parentId, 'key' => 'x', 'filename' => 'x'], [], $info);
        $this->assertFalse($byId['success']);
        $this->assertSame(
            'not allowed to create ' . $folderType . ' in parent id ' . $parentId,
            $byId['message']
        );

        $path = $this->parent->getRealFullPath();
        $byPath = $resolver(null, ['path' => $path, 'key' => 'x', 'filename' => 'x'], [], $info);
        $this->assertFalse($byPath['success']);
        $this->assertSame(
            'not allowed to create ' . $folderType . " in parent path '" . $path . "'",
            $byPath['message']
        );
    }

    /**
     * The create asset and create document resolvers are closures made by the schema builders; they are built by hand.
     */
    public function testCreateAssetDeniedMessageNamesParent(): void
    {
        $this->parent = TestHelper::createAssetFolder(self::PREFIX);
        $config = ['fields' => []];
        $this->mutationType()->buildCreateAssetMutation($config, ['configuration' => $this->configuration()]);

        $resolver = $config['fields']['createAsset']['resolve'];
        $this->assertDeniedMessages($resolver, 'not allowed to create asset', ['filename' => 'x', 'type' => 'text']);
    }

    public function testCreateDocumentDeniedMessageNamesParent(): void
    {
        $this->parent = TestHelper::createDocumentFolder(self::PREFIX);
        $config = ['fields' => []];
        $this->mutationType()->buildUpdateDocumentMutation(
            $config,
            ['configuration' => $this->configuration()],
            'create',
            'link'
        );

        $resolver = $config['fields']['createDocumentLink']['resolve'];
        $this->assertDeniedMessages($resolver, 'not allowed to create document', ['key' => 'x']);
    }

    /**
     * @param array<string, mixed> $baseArgs
     */
    private function assertDeniedMessages(callable $resolver, string $message, array $baseArgs): void
    {
        $info = $this->createStub(ResolveInfo::class);
        $parentId = $this->parent->getId();
        $path = $this->parent->getRealFullPath();

        $byId = $resolver(null, ['parentId' => $parentId] + $baseArgs, [], $info);
        $this->assertFalse($byId['success']);
        $this->assertSame($message . ' in parent id ' . $parentId, $byId['message']);

        $byPath = $resolver(null, ['path' => $path] + $baseArgs, [], $info);
        $this->assertFalse($byPath['success']);
        $this->assertSame($message . " in parent path '" . $path . "'", $byPath['message']);
    }

    private function configuration(): Configuration
    {
        return new Configuration('graphql', '/1', self::CONFIG_NAME, [
            'schema' => ['specialEntities' => [
                'asset' => ['create' => true],
                'document' => ['update' => true],
            ]],
            'security' => ['skipPermissionCheck' => false],
        ]);
    }

    private function mutationType(): MutationType
    {
        // the service is final and needs the whole container: set just the lookups the mutation builders use
        $graphQlService = (new \ReflectionClass(Service::class))->newInstanceWithoutConstructor();
        $assetTypes = new Container();
        $assetTypes->set('asset', new class() {
            public function build(): Type
            {
                return Type::string();
            }
        });
        $this->setProperty($graphQlService, 'assetTypeGeneratorFactories', $assetTypes);
        $this->setProperty($graphQlService, 'assetDataTypes', ['asset_input' => Type::string()]);
        $documentTypes = ['document_link' => Type::string(), 'document_link_input' => Type::string()];
        $this->setProperty($graphQlService, 'documentDataTypes', $documentTypes);

        $mutationType = (new \ReflectionClass(MutationType::class))->newInstanceWithoutConstructor();
        $mutationType->setGraphQLService($graphQlService);
        $this->setProperty($mutationType, 'eventDispatcher', new EventDispatcher());

        return $mutationType;
    }

    private function setProperty(object $object, string $property, mixed $value): void
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setValue($object, $value);
    }

    private function createFolderResolver(string $elementType): callable
    {
        // the constructor builds the whole schema; the folder resolver only needs the (default) permission check flag
        $mutationType = (new \ReflectionClass(MutationType::class))->newInstanceWithoutConstructor();

        return $mutationType->getCreateFolderResolver($elementType);
    }
}
