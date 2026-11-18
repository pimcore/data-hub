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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\Resolver;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\MultihrefMetadata;
use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\ObjectMetadata;
use Pimcore\Model\DataObject\Data\ElementMetadata;
use Pimcore\Model\DataObject\Data\ObjectMetadata as ObjectMetadataModel;

/**
 * The `keys` argument of the link `metadata` of advanced many-to-many relations.
 */
class RelationMetadataKeysTest extends Unit
{
    private const COLUMNS = ['altName', 'note', 'position'];

    private const DATA = ['altName' => 'OnSale', 'note' => 'n', 'position' => '1'];

    public function testElementMetadataWithoutKeysReturnsAllColumns(): void
    {
        $this->assertSame(
            ['altName' => 'OnSale', 'note' => 'n', 'position' => '1'],
            $this->namesToValues((new MultihrefMetadata())->resolveMetadata($this->value($this->elementMetadata())))
        );
    }

    public function testElementMetadataKeysLimitTheColumns(): void
    {
        $this->assertSame(
            ['note' => 'n', 'position' => '1'],
            $this->namesToValues((new MultihrefMetadata())->resolveMetadata(
                $this->value($this->elementMetadata()),
                ['keys' => ['position', 'note', 'unknown']]
            ))
        );
    }

    public function testObjectMetadataKeysLimitTheColumns(): void
    {
        $metadata = new ObjectMetadataModel('links', self::COLUMNS);
        $metadata->setData(self::DATA);

        $this->assertSame(
            ['altName' => 'OnSale'],
            $this->namesToValues((new ObjectMetadata())->resolveMetadata($this->value($metadata), ['keys' => ['altName']]))
        );
    }

    public function testKeysThatMatchNothingReturnAnEmptyList(): void
    {
        $this->assertSame(
            [],
            (new MultihrefMetadata())->resolveMetadata($this->value($this->elementMetadata()), ['keys' => ['unknown']])
        );
    }

    private function elementMetadata(): ElementMetadata
    {
        $metadata = new ElementMetadata('links', self::COLUMNS);
        $metadata->setData(self::DATA);

        return $metadata;
    }

    /**
     * The value graphql-php hands to the `metadata` resolver: the relation item with its link.
     *
     * @return array{element: array{__relation: ElementMetadata|ObjectMetadataModel}}
     */
    private function value(ElementMetadata|ObjectMetadataModel $metadata): array
    {
        return ['element' => ['__relation' => $metadata]];
    }

    /**
     * @param array<int, array{name: string, value: mixed}>|null $metadata
     *
     * @return array<string, mixed>
     */
    private function namesToValues(?array $metadata): array
    {
        return array_column($metadata ?? [], 'value', 'name');
    }
}
