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
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use Pimcore\Db;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\Layout;
use Pimcore\Model\DataObject\Listing;

/**
 * @internal
 */
class Helper
{
    /**
     * @param Listing\Concrete $list
     * @param \stdClass | array $filter
     * @param array $columns
     * @param array $mappingTable
     */
    public static function addJoins(&$list, $filter, $columns, &$mappingTable = [])
    {
        $filterEntries = is_array($filter) ? $filter : [$filter];

        foreach ($filterEntries as $entry) {
            $parts = get_object_vars($entry);

            foreach ($parts as $key => $value) {
                foreach ($columns as $column) {
                    $attributes = $column['attributes'];

                    if (isset($attributes['attribute'])) {
                        $name = $attributes['attribute'];

                        if (strpos($name, '~') !== false) {
                            $nameParts = explode('~', $name);
                            $brickName = $nameParts[0];
                            $brickKey = $nameParts[1];
                            $list->addObjectbrick($brickName);
                            $mappingTable[$brickKey] = 1;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $defaultTable
     * @param string|array|\stdClass $q
     * @param string|null $op
     * @param string|null $subject
     * @param array $fieldMappingTable
     *
     * @return string
     */
    public static function buildSqlCondition($defaultTable, $q, $op = null, $subject = null, $fieldMappingTable = [])
    {
        // Examples:
        //
        //q={"o_modificationDate" : {"$gt" : "1000"}}
        //where ((`o_modificationDate` > '1000') )
        //
        //
        //
        //
        //q=[{"o_modificationDate" : {"$gt" : "1000"}}, {"o_modificationDate" : {"$lt" : "9999"}}]
        //where ( ((`o_modificationDate` > '1000') )  AND  ((`o_modificationDate` < '9999') )  )
        //
        //
        //
        //
        //q={"o_modificationDate" : {"$gt" : "1000"}, "$or": [{"o_id": "3", "o_key": {"$like" :"%lorem-ipsum%"}}]}
        //where ((`o_modificationDate` > '1000') AND  ((`o_id` = '3') OR  ((`o_key` LIKE '%lorem-ipsum%') )  )  )
        //
        // q={"$and" : [{"o_published": "0"}, {"o_modificationDate" : {"$gt" : "1000"}, "$or": [{"o_id": "3", "o_key": {"$like" :"%lorem-ipsum%"}}]}]}
        //
        // where ( ((`o_published` = '0') )  AND  ((`o_modificationDate` > '1000') AND  ((`o_id` = '3') OR (`o_key` LIKE '%lorem-ipsum%') )  )  )

        if (!$op) {
            $op = 'AND';
        }
        $mappingTable = [
            '$gt' => '>',
            '$gte' => '>=',
            '$lt' => '<',
            '$lte' => '<=',
            '$like' => 'LIKE',
            '$notlike' => 'NOT LIKE',
            '$notnull' => 'IS NOT NULL',
            '$not' => 'NOT'
        ];
        $ops = array_keys($mappingTable);

        $db = Db::get();

        $parts = [];
        if (is_string($q)) {
            return $q;
        }

        foreach ($q as $key => $value) {
            if (array_search(strtolower($key), ['$and', '$or']) !== false) {
                $childOp = strtolower($key) == '$and' ? 'AND' : 'OR';

                if (is_array($value)) {
                    $childParts = [];
                    foreach ($value as $arrItem) {
                        $childParts[] = self::buildSqlCondition(
                            $defaultTable,
                            $arrItem,
                            $childOp,
                            $subject,
                            $fieldMappingTable
                        );
                    }
                    $parts[] = implode(' ' . $childOp . ' ', $childParts);
                } else {
                    $parts[] = self::buildSqlCondition($defaultTable, $value, $childOp);
                }
            } else {
                if (is_array($value)) {
                    foreach ($value as $subValue) {
                        $parts[] = self::buildSqlCondition($defaultTable, $subValue);
                    }
                } elseif ($value instanceof \stdClass) {
                    $objectVars = get_object_vars($value);
                    foreach ($objectVars as $objectVar => $objectValue) {
                        if (array_search(strtolower($objectVar), $ops) !== false) {
                            $innerOp = $mappingTable[strtolower($objectVar)];
                            if ($innerOp == 'NOT') {
                                $valuePart = ' IS NULL';
                                if (!is_null($objectValue)) {
                                    $valuePart = ' =' . $db->quote($objectValue);
                                }

                                if (isset($fieldMappingTable[$key])) {
                                    $parts[] = '( NOT ' . $db->quoteIdentifier($key) . $valuePart . ')';
                                } else {
                                    $parts[] = '( NOT ' . self::quoteAbsoluteColumnName(
                                            $defaultTable,
                                            $key
                                        ) . $valuePart . ')';
                                }
                            } else {
                                $parts[] = '(' . self::quoteAbsoluteColumnName(
                                        $defaultTable,
                                        $key
                                    ) . ' ' . $innerOp . ' ' . $db->quote($objectValue) . ')';
                            }
                        } else {
                            if ($objectValue instanceof \stdClass) {
                                $parts[] = self::buildSqlCondition($defaultTable, $objectValue, null, $objectVar);
                            } else {
                                if (is_null($objectValue)) {
                                    $parts[] = '(' . self::quoteAbsoluteColumnName(
                                            $defaultTable,
                                            $objectVar
                                        ) . ' IS NULL)';
                                } else {
                                    $parts[] = '(' . self::quoteAbsoluteColumnName(
                                            $defaultTable,
                                            $objectVar
                                        ) . ' = ' . $db->quote($objectValue) . ')';
                                }
                            }
                        }
                    }
                    $combinedParts = implode(' ' . $op . ' ', $parts);
                    $parts = [$combinedParts];
                } else {
                    if (array_search(strtolower($key), $ops) !== false) {
                        $innerOp = $mappingTable[strtolower($key)];
                        if ($innerOp == 'NOT') {
                            $parts[] = '(NOT' . self::quoteAbsoluteColumnName(
                                    $defaultTable,
                                    $subject
                                ) . ' = ' . $db->quote($value) . ')';
                        } else {
                            $parts[] = '(' . self::quoteAbsoluteColumnName(
                                    $defaultTable,
                                    $subject
                                ) . ' ' . $innerOp . ' ' . $db->quote($value) . ')';
                        }
                    } else {
                        if (isset($fieldMappingTable[$key])) {
                            if (is_null($value)) {
                                $parts[] = '(' . $db->quoteIdentifier($key) . ' IS NULL)';
                            } else {
                                $parts[] = '(' . $db->quoteIdentifier($key) . ' = ' . $db->quote($value) . ')';
                            }
                        } else {
                            if (is_null($value)) {
                                $parts[] = '(' . self::quoteAbsoluteColumnName($defaultTable, $key) . ' IS NULL)';
                            } else {
                                $parts[] = '(' . self::quoteAbsoluteColumnName(
                                        $defaultTable,
                                        $key
                                    ) . ' = ' . $db->quote($value) . ')';
                            }
                        }
                    }
                }
            }
        }

        $subCondition = ' (' . implode(' ' . $op . ' ', $parts) . ' ) ';

        return $subCondition;
    }

    /**
     * @param string $defaultTable
     * @param string $columnName
     *
     * @return string
     */
    protected static function quoteAbsoluteColumnName($defaultTable, $columnName)
    {
        $db = Db::get();
        $absoluteColumnName = (strpos($columnName, '.') !== false) ? $columnName : $defaultTable . '.' . $columnName;

        return $db->quoteIdentifier($absoluteColumnName);
    }

    /**
     * @param Layout|Data $def
     */
    public static function extractDataDefinitions($def, &$fieldDefinitions = [])
    {
        if ($def instanceof Layout || $def instanceof Data\Block || $def instanceof Data\Localizedfields) {
            if ($def->hasChildren()) {
                foreach ($def->getChildren() as $child) {
                    self::extractDataDefinitions($child, $fieldDefinitions);
                }
            }
        } elseif ($def instanceof Data) {
            $fieldDefinitions[$def->getName()] = $def;
        }
    }
}
