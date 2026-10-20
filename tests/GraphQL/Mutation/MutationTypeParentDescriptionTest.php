<?php

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
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;

/**
 * The "not allowed to create ..." messages name the parent the caller asked for,
 * so a wrong value passed through indirection is easy to spot.
 */
class MutationTypeParentDescriptionTest extends Unit
{
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
}
