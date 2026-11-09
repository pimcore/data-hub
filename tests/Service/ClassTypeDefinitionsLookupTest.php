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

namespace Pimcore\Bundle\DataHubBundle\Tests\Service;

use Codeception\Test\Unit;
use Doctrine\DBAL\Connection;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectTypeFactory;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\DataObject\ClassDefinition;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Building the GraphQL types resolves the class definitions with one query, not with one name lookup per class.
 */
class ClassTypeDefinitionsLookupTest extends Unit
{
    private const CLASSES = ['pme777Alpha' => 'pme777_a', 'pme777Beta' => 'pme777_b', 'pme777Gamma' => 'pme777_c'];

    /** @var string[] */
    private array $queries = [];

    private ?KernelInterface $previousKernel;

    /** @var array<string, array{bool, mixed}> */
    private array $previousCache = [];

    protected function setUp(): void
    {
        $this->previousKernel = \Pimcore::getKernel();
        $this->queries = [];

        foreach (self::CLASSES as $name => $id) {
            $key = 'class_' . $id;
            $this->previousCache[$key] = [RuntimeCache::isRegistered($key), RuntimeCache::isRegistered($key) ? RuntimeCache::get($key) : null];
            $definition = new ClassDefinition();
            $definition->setName($name);
            $definition->setId($id);
            RuntimeCache::set($key, $definition);
        }

        $connection = $this->createMock(Connection::class);
        $connection->method('quoteIdentifier')->willReturnCallback(fn (string $identifier) => '`' . $identifier . '`');
        $connection->method('fetchAllAssociative')->willReturnCallback(function (string $sql, array $params = []) {
            $this->queries[] = $sql;
            $rows = [];
            foreach (self::CLASSES as $name => $id) {
                $rows[] = ['id' => $id, 'name' => $name];
            }

            return str_contains($sql, 'WHERE')
                ? array_values(array_filter($rows, fn (array $row) => $row['name'] === ($params[0] ?? null)))
                : $rows;
        });

        $container = new Container();
        $container->set('doctrine.dbal.default_connection', $connection);
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('getContainer')->willReturn($container);
        $this->setKernel($kernel);

        ClassTypeDefinitions::$definitions = [];
        DataObjectTypeFactory::$registry = [];
    }

    protected function tearDown(): void
    {
        $this->setKernel($this->previousKernel);
        foreach ($this->previousCache as $key => [$wasRegistered, $value]) {
            if ($wasRegistered) {
                RuntimeCache::set($key, $value);
            } elseif (RuntimeCache::isRegistered($key)) {
                RuntimeCache::getInstance()->offsetUnset($key);
            }
        }
        ClassTypeDefinitions::$definitions = [];
        DataObjectTypeFactory::$registry = [];
    }

    public function testBuildResolvesAllClassesWithOneQuery(): void
    {
        $service = $this->buildService();

        ClassTypeDefinitions::build($service, []);

        $this->assertSame(array_keys(self::CLASSES), array_keys(ClassTypeDefinitions::$definitions));
        foreach (self::CLASSES as $name => $id) {
            $this->assertSame($id, ClassTypeDefinitions::$definitions[$name]->classId);
            $this->assertTrue(ClassTypeDefinitions::$definitions[$name]->built);
        }
        $this->assertCount(1, $this->queries, 'class definitions are loaded with one query: ' . implode(' | ', $this->queries));
    }

    public function testLoadedClassIsServedWithoutQuery(): void
    {
        $service = $this->buildService();
        ClassTypeDefinitions::build($service, []);
        $this->queries = [];

        $class = ClassTypeDefinitions::getClassDefinition('pme777Beta');

        $this->assertSame('pme777_b', $class?->getId());
        $this->assertSame([], $this->queries);
    }

    public function testUnknownClassIsLookedUpByName(): void
    {
        $service = $this->buildService();
        ClassTypeDefinitions::build($service, []);
        $this->queries = [];

        $this->assertNull(ClassTypeDefinitions::getClassDefinition('pme777Missing'));
        $this->assertCount(1, $this->queries);
        $this->assertStringContainsString('WHERE', $this->queries[0]);
    }

    private function buildService(): Service
    {
        // the service is final and needs the whole container: set just the factory lookup the type builder uses
        $service = (new ReflectionClass(Service::class))->newInstanceWithoutConstructor();
        $factories = new Container();
        $factories->set('object', new DataObjectTypeFactory($service, ClassTypeStub::class));
        (new ReflectionProperty(Service::class, 'generalTypeGeneratorFactories'))->setValue($service, $factories);

        return $service;
    }

    private function setKernel(?KernelInterface $kernel): void
    {
        $property = new ReflectionProperty(\Pimcore::class, 'kernel');
        $property->setValue(null, $kernel);
    }
}

/**
 * @internal
 */
final class ClassTypeStub
{
    public bool $built = false;

    public function __construct(public Service $service, public string $name, public mixed $classId, public array $config = [], public array $context = [])
    {
    }

    public function build(array $context = []): void
    {
        $this->built = true;
    }
}
