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

use Carbon\CarbonPeriod;
use Codeception\Test\Unit;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureQueryTypeGenerator\DateRange;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureQueryTypeGenerator\NumericRange;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureQueryTypeGenerator\QuantityValueRange;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureQueryTypeGenerator\Table;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureQueryTypeGenerator\User;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\TableType;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeInterface\CsFeature;
use Pimcore\Bundle\DataHubBundle\Tests\GraphQL\ServiceStubTrait;
use Pimcore\Model\DataObject\Data\NumericRange as NumericRangeValue;
use Pimcore\Model\DataObject\Data\QuantityValueRange as QuantityValueRangeValue;
use Pimcore\Model\DataObject\QuantityValue\Unit as QuantityValueUnit;

/**
 * The read side of the types of pimcore/data-hub#53: one shared type per fieldtype, resolved from the value
 * the store holds. The generators are asked for their type and the type is executed, so a field renamed on
 * either side fails here.
 *
 * @internal
 */
class GuardedTypesReadTest extends Unit
{
    use ServiceStubTrait;

    private const UNIT_ID = 'dh-cs-read-unit';

    private const VALUE = 'value';

    private const MINIMUM = 'minimum';

    private const MAXIMUM = 'maximum';

    private const NUMERIC_RANGE = 'numericRange';

    private const QUANTITY_VALUE_RANGE = 'quantityValueRange';

    private const NUMERIC_RANGE_SELECTION = '{ ' . self::NUMERIC_RANGE . ' { minimum maximum } }';

    public function testANumericRangePublishesBothBoundsAndLeavesAnOpenOneNull(): void
    {
        $type = (new NumericRange($this->createServiceStub()))->getFieldType();
        $this->assertSame('csFeatureNumericRange', $type->name);

        $data = $this->read($type, new NumericRangeValue(1, 7), self::NUMERIC_RANGE_SELECTION);
        $this->assertSame([self::MINIMUM => 1.0, self::MAXIMUM => 7.0], $data[self::NUMERIC_RANGE]);

        $data = $this->read($type, new NumericRangeValue(null, 7), self::NUMERIC_RANGE_SELECTION);
        $this->assertSame([self::MINIMUM => null, self::MAXIMUM => 7.0], $data[self::NUMERIC_RANGE]);
    }

    public function testANumericRangeOfAnEmptySlotPublishesNull(): void
    {
        $type = (new NumericRange($this->createServiceStub()))->getFieldType();

        $data = $this->read($type, null, self::NUMERIC_RANGE_SELECTION);

        $this->assertNull($data[self::NUMERIC_RANGE]);
    }

    public function testAQuantityValueRangePublishesTheBoundsAndTheUnit(): void
    {
        $type = (new QuantityValueRange($this->createServiceStub()))->getFieldType();
        $this->assertSame('csFeatureQuantityValueRange', $type->name);

        $unit = new QuantityValueUnit();
        $unit->setId(self::UNIT_ID);
        $unit->setAbbreviation('dhcs');
        $unit->setLongname('read test unit');

        $data = $this->read(
            $type,
            new QuantityValueRangeValue(1, 7, $unit),
            '{ quantityValueRange { minimum maximum unit { id abbreviation longname } } }'
        );

        $this->assertSame(1.0, $data[self::QUANTITY_VALUE_RANGE][self::MINIMUM]);
        $this->assertSame(7.0, $data[self::QUANTITY_VALUE_RANGE][self::MAXIMUM]);
        $this->assertSame(
            ['id' => self::UNIT_ID, 'abbreviation' => 'dhcs', 'longname' => 'read test unit'],
            $data[self::QUANTITY_VALUE_RANGE]['unit']
        );
    }

    public function testADateRangePublishesBothBoundsAndTakesACarbonFormat(): void
    {
        $type = (new DateRange($this->createServiceStub()))->getFieldType();
        $this->assertSame('csFeatureDateRange', $type->name);

        $period = CarbonPeriod::create(
            (new \Carbon\Carbon())->setTimestamp(1700000000),
            (new \Carbon\Carbon())->setTimestamp(1700086400)
        );

        $data = $this->read(
            $type,
            $period,
            '{ dateRange { startDate(format: "U") endDate(format: "U") } }'
        );

        $this->assertSame(['startDate' => '1700000000', 'endDate' => '1700086400'], $data['dateRange']);

        $data = $this->read($type, $period, '{ dateRange { startDate } }');
        $this->assertIsString($data['dateRange']['startDate']);
    }

    public function testATablePublishesItsRowsByPosition(): void
    {
        $type = (new Table($this->createServiceStub()))->getFieldType();
        $this->assertSame('csFeatureTable', $type->name);

        $data = $this->read($type, [['a', 'b'], ['c', 'd']], '{ rows }');

        $this->assertSame([['a', 'b'], ['c', 'd']], $data['rows']);
    }

    /**
     * A key with an active column configuration keeps its rows by column name, and the names belong to that
     * one key, not to the single type every table key is published as.
     */
    public function testATableWithNamedColumnsPublishesThePositionsOfThoseColumns(): void
    {
        $this->assertSame(
            [['1', '2']],
            TableType::rows([['first' => 1, 'second' => 2]])
        );
        $this->assertNull(TableType::rows('a,b'));
    }

    public function testAUserPublishesTheStoredIdAsAString(): void
    {
        $type = (new User($this->createServiceStub()))->getFieldType();
        $this->assertSame('csFeatureUser', $type->name);

        $data = $this->read($type, '22', '{ userId }');

        $this->assertSame('22', $data['userId']);
    }

    /**
     * @return array<string, mixed>
     */
    private function read(ObjectType $type, mixed $storedValue, string $selection): array
    {
        $descriptor = new FeatureDescriptor();
        $descriptor->setValue($storedValue);

        $schema = new Schema([
            'query' => new ObjectType([
                'name' => 'GuardedTypesReadTestQuery',
                'fields' => [
                    self::VALUE => [
                        'type' => $type,
                        'resolve' => static fn (): FeatureDescriptor => $descriptor,
                    ],
                ],
            ]),
            'types' => [CsFeature::getInstance()],
        ]);
        $schema->assertValid();

        $result = GraphQL::executeQuery($schema, '{ ' . self::VALUE . ' ' . $selection . ' }')->toArray();

        $this->assertArrayNotHasKey('errors', $result, json_encode($result['errors'] ?? []));

        return $result['data'][self::VALUE];
    }
}
