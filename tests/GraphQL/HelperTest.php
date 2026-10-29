<?php

declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\GraphQL\Helper;

class HelperTest extends Unit
{
    public function testBuildSqlConditionQuotesFieldConditions(): void
    {
        $filter = json_decode('{"modificationDate": {"$gt": "1000"}, "$or": [{"id": "3"}, {"key": {"$like": "%lorem%"}}]}');

        $condition = Helper::buildSqlCondition('objects', $filter);

        $this->assertSame(
            " ((`objects`.`modificationDate` > '1000') AND  ((`objects`.`id` = '3') )  OR  ((`objects`.`key` LIKE '%lorem%') )  ) ",
            $condition
        );
    }

    public function testBuildSqlConditionCombinesTopLevelArrayEntries(): void
    {
        $filter = json_decode('[{"id": {"$gte": "1"}}, {"id": {"$lte": "9"}}]');

        $condition = Helper::buildSqlCondition('objects', $filter);

        $this->assertSame(
            " ( ((`objects`.`id` >= '1') )  AND  ((`objects`.`id` <= '9') )  ) ",
            $condition
        );
    }

    /**
     * @dataProvider provideNonObjectFilters
     */
    public function testBuildSqlConditionRejectsConditionsThatAreNotObjects(string $json): void
    {
        $this->expectException(ClientSafeException::class);

        Helper::buildSqlCondition('objects', json_decode($json));
    }

    public function testAddJoinsSkipsEntriesThatAreNotObjects(): void
    {
        $list = null;
        $mappingTable = [];

        Helper::addJoins($list, json_decode('["1=1"]'), [], $mappingTable);

        $this->assertSame([], $mappingTable);
    }

    public function provideNonObjectFilters(): array
    {
        return [
            'top-level string' => ['"1=1"'],
            'top-level number' => ['1'],
            'string in $and' => ['{"$and": ["1=1"]}'],
            'string as $or value' => ['{"$or": "1=1"}'],
            'string in field array' => ['{"id": ["1=1"]}'],
            'string next to objects in $and' => ['{"$and": [{"id": "1"}, "1=1"]}'],
        ];
    }
}
