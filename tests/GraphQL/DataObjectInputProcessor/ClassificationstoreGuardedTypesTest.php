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

use Carbon\CarbonPeriod;
use Codeception\Test\Unit;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\NumericRange;
use Pimcore\Model\DataObject\Data\QuantityValueRange;
use Pimcore\Model\DataObject\QuantityValue\Unit as QuantityValueUnit;
use Pimcore\Model\User;

/**
 * The types of pimcore/data-hub#53 whose editmode conversion cannot be handed a mutation value as it stands:
 * it reads members without a default, casts a value core's own editor never sends, or answers an unusable
 * value with null, which would clear the slot instead of writing it. Each one is refused with a client-safe
 * message, and a reasonable value round trips.
 *
 * @internal
 */
class ClassificationstoreGuardedTypesTest extends Unit
{
    use ClassificationstoreFixtureTrait;

    private const IDENTIFIER_GROUP = 'datahub_guarded_types_group';

    private const NUMERIC_RANGE = 'numericRange';

    private const QUANTITY_VALUE_RANGE = 'quantityValueRange';

    private const DATE_RANGE = 'dateRange';

    private const TABLE = 'table';

    private const USER = 'user';

    private const TYPES = [
        self::NUMERIC_RANGE,
        self::QUANTITY_VALUE_RANGE,
        self::DATE_RANGE,
        self::TABLE,
        self::USER,
    ];

    private const NOT_SAVED = 'Nothing was saved.';

    private const NOT_A_NUMBER = 'is not a number';

    private const NOT_ROWS = 'list of rows';

    private const DOES_NOT_EXIST = 'does not exist';

    private const UNIT = 'unit';

    private const MINIMUM = 'minimum';

    private const MAXIMUM = 'maximum';

    private const START_DATE = 'startDate';

    private const END_DATE = 'endDate';

    /** @var QuantityValueUnit[] */
    private array $units = [];

    /** @var User[] */
    private array $users = [];

    protected function _after(): void
    {
        $this->deleteFixtures();
        foreach ($this->units as $unit) {
            $unit->delete();
        }
        foreach ($this->users as $user) {
            $user->delete();
        }
        $this->units = $this->users = [];
    }

    public function testTheGuardedTypesAreWritableWhenTheReadSidePublishesThem(): void
    {
        $this->readable = self::TYPES;
        $processor = $this->createProcessor();

        foreach (self::TYPES as $type) {
            $this->assertContains($type, $processor->getAcceptedTypes());
        }
    }

    public function testANumericRangeRoundTrips(): void
    {
        $object = $this->prepare(self::NUMERIC_RANGE);

        $result = $this->write($object, [self::MINIMUM => 1, self::MAXIMUM => 7]);

        $this->assertTrue($result['success'], $result['message']);
        $stored = $this->stored($object);
        $this->assertInstanceOf(NumericRange::class, $stored);
        $this->assertEquals(1, $stored->getMinimum());
        $this->assertEquals(7, $stored->getMaximum());
    }

    public function testANumericRangeTakesOneBoundAndLeavesTheOtherOpen(): void
    {
        $object = $this->prepare(self::NUMERIC_RANGE);

        $result = $this->write($object, [self::MAXIMUM => 7]);

        $this->assertTrue($result['success'], $result['message']);
        $stored = $this->stored($object);
        $this->assertInstanceOf(NumericRange::class, $stored);
        $this->assertNull($stored->getMinimum());
        $this->assertEquals(7, $stored->getMaximum());
    }

    public function testANumericRangeRefusesABoundThatIsNotANumber(): void
    {
        $object = $this->prepare(self::NUMERIC_RANGE);

        $result = $this->write($object, [self::MINIMUM => 'one', self::MAXIMUM => 7]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString(self::NOT_A_NUMBER, $result['message']);
        $this->assertNull($this->stored($object));
    }

    public function testANumericRangeRefusesAValueThatIsNotAMap(): void
    {
        $object = $this->prepare(self::NUMERIC_RANGE);

        $result = $this->write($object, '1-7');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString(self::NOT_SAVED, $result['message']);
        $this->assertNull($this->stored($object));
    }

    public function testAQuantityValueRangeRoundTrips(): void
    {
        $object = $this->prepare(self::QUANTITY_VALUE_RANGE);
        $unit = $this->createUnit();

        $result = $this->write($object, [self::MINIMUM => 1, self::MAXIMUM => 7, self::UNIT => $unit->getId()]);

        $this->assertTrue($result['success'], $result['message']);
        $stored = $this->stored($object);
        $this->assertInstanceOf(QuantityValueRange::class, $stored);
        $this->assertEquals(1, $stored->getMinimum());
        $this->assertEquals(7, $stored->getMaximum());
        $this->assertSame($unit->getId(), $stored->getUnitId());
    }

    public function testAQuantityValueRangeRefusesABoundThatIsNotANumberInsteadOfStoringZero(): void
    {
        $object = $this->prepare(self::QUANTITY_VALUE_RANGE);
        $unit = $this->createUnit();

        $result = $this->write($object, [self::MINIMUM => 'one', self::MAXIMUM => 7, self::UNIT => $unit->getId()]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString(self::NOT_A_NUMBER, $result['message']);
        $this->assertNull($this->stored($object));
    }

    public function testAQuantityValueRangeRefusesAUnitThatDoesNotExist(): void
    {
        $object = $this->prepare(self::QUANTITY_VALUE_RANGE);

        $result = $this->write($object, [self::MINIMUM => 1, self::MAXIMUM => 7, self::UNIT => 'dh-cs-no-such-unit']);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString(self::DOES_NOT_EXIST, $result['message']);
        $this->assertNull($this->stored($object));
    }

    public function testADateRangeRoundTripsInMilliseconds(): void
    {
        $object = $this->prepare(self::DATE_RANGE);

        $result = $this->write($object, [self::START_DATE => 1700000000000, self::END_DATE => 1700086400000]);

        $this->assertTrue($result['success'], $result['message']);
        $stored = $this->stored($object);
        $this->assertInstanceOf(CarbonPeriod::class, $stored);
        $this->assertSame(1700000000, $stored->getStartDate()->getTimestamp());
        $this->assertSame(1700086400, $stored->getEndDate()->getTimestamp());
    }

    public function testADateRangeRefusesOneBoundAloneInsteadOfClearingTheSlot(): void
    {
        $object = $this->prepare(self::DATE_RANGE);

        $result = $this->write($object, [self::START_DATE => 1700000000000, self::END_DATE => 1700086400000]);
        $this->assertTrue($result['success'], $result['message']);

        $result = $this->write($object, [self::START_DATE => 1700000000000]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('needs both', $result['message']);
        $this->assertInstanceOf(CarbonPeriod::class, $this->stored($object));
    }

    public function testADateRangeRefusesABoundThatIsNotANumber(): void
    {
        $object = $this->prepare(self::DATE_RANGE);

        $result = $this->write($object, [self::START_DATE => '2023-11-14', self::END_DATE => 1700086400000]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString(self::NOT_A_NUMBER, $result['message']);
        $this->assertNull($this->stored($object));
    }

    /**
     * One spelling only: the bounds are named the way the read side publishes them, and the snake case of
     * core's own editmode conversion is an implementation detail rather than a second accepted spelling.
     */
    public function testADateRangeRefusesTheEditmodeMemberNames(): void
    {
        $object = $this->prepare(self::DATE_RANGE);

        $result = $this->write($object, ['start_date' => 1700000000000, 'end_date' => 1700086400000]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('startDate', $result['message']);
        $this->assertNull($this->stored($object));
    }

    public function testATableRoundTrips(): void
    {
        $object = $this->prepare(self::TABLE);

        $result = $this->write($object, [['a', 'b'], ['c', 'd']]);

        $this->assertTrue($result['success'], $result['message']);
        $this->assertSame([['a', 'b'], ['c', 'd']], $this->stored($object));
    }

    /**
     * The class field route stores the rows it is given and never runs the editmode conversion, whose emptiness
     * check answers an all-blank row with null. Both routes write the same value the same way.
     */
    public function testATableStoresAnAllBlankRowInsteadOfClearingTheKey(): void
    {
        $object = $this->prepare(self::TABLE);

        $result = $this->write($object, [['', '']]);

        $this->assertTrue($result['success'], $result['message']);
        $this->assertSame([['', '']], $this->stored($object));
    }

    /**
     * empty('0') is true, so core's emptiness check reads a row holding only "0" as an empty table.
     */
    public function testATableStoresARowHoldingOnlyAZeroInsteadOfClearingTheKey(): void
    {
        $object = $this->prepare(self::TABLE);

        $result = $this->write($object, [['0', '']]);

        $this->assertTrue($result['success'], $result['message']);
        $this->assertSame([['0', '']], $this->stored($object));
    }

    public function testATableRefusesAValueThatIsNotRows(): void
    {
        $object = $this->prepare(self::TABLE);

        $result = $this->write($object, 'a,b');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString(self::NOT_ROWS, $result['message']);
        $this->assertNull($this->stored($object));
    }

    public function testATableRefusesARowThatIsNotAListOfCells(): void
    {
        $object = $this->prepare(self::TABLE);

        $result = $this->write($object, [['a', ['b']]]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString(self::NOT_ROWS, $result['message']);
        $this->assertNull($this->stored($object));
    }

    public function testAUserRoundTrips(): void
    {
        $object = $this->prepare(self::USER);
        $user = $this->createUser();

        $result = $this->write($object, $user->getId());

        $this->assertTrue($result['success'], $result['message']);
        $this->assertSame((string) $user->getId(), $this->stored($object));
    }

    public function testAUserRefusesAValueThatIsNotAnId(): void
    {
        $object = $this->prepare(self::USER);

        $result = $this->write($object, 'admin');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('positive integer', $result['message']);
        $this->assertNull($this->stored($object));
    }

    public function testAUserRefusesAnIdThatIsNotAUserInsteadOfClearingTheSlot(): void
    {
        $object = $this->prepare(self::USER);
        $user = $this->createUser();

        $result = $this->write($object, $user->getId());
        $this->assertTrue($result['success'], $result['message']);

        $result = $this->write($object, 99999999);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString(self::DOES_NOT_EXIST, $result['message']);
        $this->assertSame((string) $user->getId(), $this->stored($object));
    }

    private function prepare(string $type): Concrete
    {
        $this->readable = self::TYPES;
        $group = $this->createGroup(self::IDENTIFIER_GROUP);
        $this->createKey($group, 'k', $type);

        return $this->createObject();
    }

    /**
     * @return array{success: bool, message: string}
     */
    private function write(Concrete $object, mixed $value): array
    {
        return $this->mutate($object, $this->input($this->groups[0], $this->relations[0], [['value' => $value]]), true);
    }

    private function stored(Concrete $object): mixed
    {
        return $this->store($object)->getLocalizedKeyValue(
            $this->groups[0]->getId(),
            $this->relations[0]->getKeyId(),
            'default'
        );
    }

    private function createUnit(): QuantityValueUnit
    {
        $unit = new QuantityValueUnit();
        $unit->setId('dh-cs-' . uniqid());
        $unit->setAbbreviation('dhcs');
        $unit->setLongname('data hub classification store test unit');
        $unit->save();
        $this->units[] = $unit;

        return $unit;
    }

    private function createUser(): User
    {
        $user = new User();
        $user->setName('dh-cs-' . uniqid());
        $user->setParentId(0);
        $user->save();
        $this->users[] = $user;

        return $user;
    }
}
