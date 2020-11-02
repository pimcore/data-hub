<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use Pimcore\Db;
use Pimcore\Db\ConnectionInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\Layout;

/**
 * @internal
 */
class Helper
{
    private static $mappingTable = [
        '$gt' => '>', '$gte' => '>=',
        '$lt' => '<', '$lte' => '<=',
        '$like' => 'LIKE', '$notlike' => 'NOT LIKE',
        '$notnull' => 'IS NOT NULL', '$not' => 'NOT'
    ];

    public static function buildSqlCondition($defaultTable, $q, $op = null, $subject = null)
    {
        if ($q instanceof \stdClass) {
            return self::buildSqlConditionRecursion($defaultTable, $q);
        }
        // Don't return input
    }

    private static function buildSqlConditionRecursion($defaultTable, $q, $op = 'AND', $subject = null)
    {
        if (is_string($q)) {
            return $q;
        }

        if (($result = self::buildInSqlCondition(Db::get(), $defaultTable, $q, $op, $subject)) !== null) {
            return $result;
        }

        if ($q instanceof \stdClass) {
            $q = get_object_vars($q);
        }

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

        $db = Db::get();
        $mappingTable = self::$mappingTable;
        $ops = array_keys($mappingTable);

        $parts = [];
        foreach ($q as $key => $value) {
            if (strtolower($key) === '$in') {
                $parts[] = self::buildInSqlCondition($db, $defaultTable, $value);
            } elseif (strtolower($key) === strtolower('$notIn')) {
                $parts[] = self::buildInSqlCondition($db, $defaultTable, $value, 'NOT');
            } elseif (array_search(strtolower($key), ['$and', '$or']) !== false) {
                $childOp = strtolower($key) == '$and' ? 'AND' : 'OR';

                if (is_array($value)) {
                    $childParts = [];
                    foreach ($value as $arrItem) {
                        $childParts[] = self::buildSqlConditionRecursion($defaultTable, $arrItem, $childOp);
                    }
                    $parts[] = implode(' ' . $childOp . ' ', $childParts);
                } else {
                    $parts[] = self::buildSqlConditionRecursion($defaultTable, $value, $childOp);
                }
            } else {
                if (is_array($value)) {
                    foreach ($value as $subValue) {
                        $parts[] = self::buildSqlConditionRecursion($defaultTable, $subValue);
                    }
                } elseif ($value instanceof \stdClass) {
                    $objectVars = get_object_vars($value);
                    foreach ($objectVars as $objectVar => $objectValue) {
                        if (array_search(strtolower($objectVar), $ops) !== false) {
                            $innerOp = $mappingTable[strtolower($objectVar)];
                            if ($innerOp == 'NOT') {
                                $parts[] = '( NOT ' . self::quoteAbsoluteColumnName($defaultTable, $key) . ' =' . $db->quote($objectValue) . ')';
                            } else {
                                $parts[] = '(' . self::quoteAbsoluteColumnName($defaultTable, $key) . ' ' . $innerOp . ' ' . $db->quote($objectValue) . ')';
                            }
                        } else {
                            if ($objectValue instanceof \stdClass) {
                                $parts[] = self::buildSqlConditionRecursion($defaultTable, $objectValue, null, $objectVar);
                            } else {
                                $parts[] = '(' . self::quoteAbsoluteColumnName($defaultTable, $objectVar) . ' = ' . $db->quote($objectValue) . ')';
                            }
                        }
                    }
                    $combinedParts = implode(' ' . $op . ' ', $parts);
                    $parts = [$combinedParts];
                } else {
                    if (array_search(strtolower($key), $ops) !== false) {
                        $innerOp = $mappingTable[strtolower($key)];
                        if ($innerOp == 'NOT') {
                            $parts[] = '(NOT' . self::quoteAbsoluteColumnName($defaultTable, $subject) . ' = ' . $db->quote($value) . ')';
                        } else {
                            $parts[] = '(' . self::quoteAbsoluteColumnName($defaultTable, $subject) . ' ' . $innerOp . ' ' . $db->quote($value) . ')';
                        }
                    } else {
                        $parts[] = '(' . self::quoteAbsoluteColumnName($defaultTable, $key) . ' = ' . $db->quote($value) . ')';
                    }
                }
            }
        }

        return ' (' . implode(' ' . $op . ' ', $parts) . ' ) ';
    }

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
        } else if ($def instanceof Data) {
            $fieldDefinitions[$def->getName()] = $def;
        }
    }

    public static function buildInSqlCondition(
        ConnectionInterface $db,
        string $table,
        $queries,
        $objectOperator = null,
        $objectField = null

    ): ?string
    {
        if ($queries instanceof \stdClass) {
            $queries = get_object_vars($queries);
            if (count($queries) === 1 && isset($queries['$or'])) {
                $objectOperator = null;
                $queries = $queries['$or'];
            }
        } elseif (!is_array($queries)) {
            $queries = [$queries]; // Only one query
        }

        $objectValues = [];
        foreach ($queries as $queryIndex => $queryData) {
            if (is_int($queryIndex) && $queryData instanceof \stdClass) {
                foreach (get_object_vars($queryData) as $newObjectField => $objectValue) {
                    if (isset($objectField) && $newObjectField !== $objectField) {
                        return null; // More then one field
                    }
                    $objectField = $newObjectField;
                    if ($objectValue instanceof \stdClass) {
                        foreach (get_object_vars($objectValue) as $newObjectOperator => $innerObjectValue) {
                            if (isset($objectOperator) && $newObjectOperator !== $objectOperator) {
                                return null; // More then one operator
                            }
                            $objectOperator = $newObjectOperator;
                            $objectValues[] = $innerObjectValue;
                        }
                    } else {
                        $objectValues = array_merge($objectValues, (array)$objectValue);
                    }
                }
            } elseif (!isset($objectField)) {
                if (strpos($queryIndex, '$') === 0) {
                    $objectField = $queryIndex;
                    $objectValues = (array)$queryData;
                } else {
                    return null; // No field found
                }
            } else {
                return null; // No in query
            }
        }

        if (empty($objectValues)) {
            return null;
        }

        $notOperator = '';
        if (isset($objectOperator)) {
            if ($objectOperator === 'OR') {
                $objectOperator = 'IN';
            } elseif ($objectOperator === 'NOT') {
                $notOperator = 'NOT ';
                $objectOperator = 'IN';
            } elseif (isset(self::$mappingTable[strtolower($objectOperator)])) {
                $objectOperator = self::$mappingTable[strtolower($objectOperator)];
            } else {
                return null; // Operator not found
            }
        } else {
            $objectOperator = 'IN';
        }

        if ($objectOperator === 'IN') {
            $objectValues = implode(', ', array_map([$db, 'quote'], $objectValues));
            return self::quoteAbsoluteColumnName($table, $objectField)
                . ' ' . $notOperator . $objectOperator . ' (' . $objectValues . ')';
        }
        if (count($objectValues) === 1) {
            return $notOperator . '( ' . self::quoteAbsoluteColumnName($table, $objectField)
                . ' ' . $objectOperator . ' ' . $db->quote($objectValues[0]) . ')';
        }

        $identifier = $db->quoteIdentifier($table === 'assets' ? 'id' : 'o_id');
        $objectSelects = [];
        foreach ($objectValues as $objectValue) {
            $objectSelects[] = 'SELECT ' . $identifier
                . ' FROM ' . $db->quoteIdentifier($table)
                . ' WHERE ' . self::quoteAbsoluteColumnName($table, $objectField)
                . ' ' . $objectOperator . ' ' . $db->quote($objectValue);
        }
        return $identifier . ' ' . $notOperator . 'IN ('
            . implode(' UNION ALL ', $objectSelects)
            . ')';
    }
}
