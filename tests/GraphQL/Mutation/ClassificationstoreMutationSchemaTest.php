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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\Mutation;

use Codeception\Test\Unit;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationFieldConfigGenerator\Classificationstore;
use Pimcore\Bundle\DataHubBundle\Tests\GraphQL\ServiceStubTrait;

/**
 * @internal
 */
class ClassificationstoreMutationSchemaTest extends Unit
{
    use ServiceStubTrait;

    private function createGenerator(): Classificationstore
    {
        return new Classificationstore($this->createServiceStub());
    }

    private function config(Classificationstore $generator, string $field): array
    {
        return $generator->getGraphQlMutationFieldConfig(['attributes' => ['attribute' => $field]], null);
    }

    public function testTheStoreIsOneInputFieldCarryingTheWholeStore(): void
    {
        $config = $this->config($this->createGenerator(), 'csfield');

        $this->assertInstanceOf(InputObjectType::class, $config['arg']);
        $this->assertSame(['groups', 'strict'], array_keys($config['arg']->getFields()));
        $this->assertIsCallable($config['processor']);
    }

    public function testSchemaWithTwoClassificationStoreFieldsIsValid(): void
    {
        $generator = $this->createGenerator();
        $first = $this->config($generator, 'one');
        $second = $this->config($generator, 'two');

        $this->assertSame($first['arg'], $second['arg']);
        $this->assertValidSchema($first['arg'], $second['arg']);

        $generator->reset();
        $third = $this->config($generator, 'one');
        $fourth = $this->config($generator, 'two');

        $this->assertNotSame($first['arg'], $third['arg']);
        $this->assertValidSchema($third['arg'], $fourth['arg']);
    }

    private function assertValidSchema(InputObjectType ...$inputs): void
    {
        $args = [];
        foreach ($inputs as $i => $input) {
            $args['a' . $i] = ['type' => $input];
        }

        $schema = new Schema([
            'query' => new ObjectType(['name' => 'Query', 'fields' => ['f' => ['type' => Type::string(), 'args' => $args]]]),
        ]);
        $schema->assertValid();
        $this->assertTrue(true);
    }
}
