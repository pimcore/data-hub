<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\Table;

use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Table;
use Pimcore\Model\DataObject\ClassDefinition\Data;


class TableTest extends \PHPUnit_Framework_TestCase
{
    const GET_TABLE_COLUMNS = "getTableColumns";

    public function setUp(): void
    {
        $service = \Pimcore::getContainer()->get("Pimcore\Bundle\DataHubBundle\GraphQL\Service");
        $this->table = new Table($service);
    }

    public function testGetTableColumnsWithWrongData()
    {
        $mockData = new Data\Input();
        $result = $this->callNotAccessibleMethod($this->table, self::GET_TABLE_COLUMNS, array($mockData));
        $this->assertEmpty($result);
    }

    public function testGetTableColumnsWithoutCols()
    {
        $mockData = new Data\Table();
        $result = $this->callNotAccessibleMethod($this->table, self::GET_TABLE_COLUMNS, array($mockData));
        $this->assertEmpty($result);
    }

    public function testGetTableColumnsWithColsWithDeactivatedColumnConfig()
    {
        $mockData = $this->createTableData();
        $mockData->setColumnConfigActivated(false);
        $result = $this->callNotAccessibleMethod($this->table, self::GET_TABLE_COLUMNS, array($mockData));
        $this->assertArrayHasKey("col0", $result);
        $this->assertArrayHasKey("col1", $result);
    }

    public function testGetTableColumnsWithColumnConfigAndKeyAsString()
    {
        $mockData = $this->createTableData([["key" => "A", "label" => "Article"], ["key" => "P", "label" => "Price"]]);
        $result = $this->callNotAccessibleMethod($this->table, self::GET_TABLE_COLUMNS, array($mockData));
        $this->assertArrayHasKey("A", $result);
        $this->assertArrayHasKey("P", $result);
    }

    private function createTableData(array $columnConfig = [], $cols = 2): Data\Table
    {
        $mockData = new Data\Table();
        $mockData->setCols($cols);
        $mockData->setColumnConfig($columnConfig);
        $mockData->setColumnConfigActivated(true);
        return $mockData;
    }

    private function callNotAccessibleMethod(mixed $class, string $methodName, array $args): mixed
    {
        $method = $this->getMethod($class, $methodName);
        return $method->invokeArgs($class, $args);
    }

    private function getMethod(mixed $classObj, string $name): \ReflectionMethod
    {
        $class = new \ReflectionClass($classObj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}

