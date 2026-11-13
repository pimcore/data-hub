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

namespace Pimcore\Bundle\DataHubBundle\Tests\Model;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Db;

/**
 * WorkspaceHelper::getReadPermissionCondition() replaced two subqueries correlated with the
 * listed table, which the database ran for every row. These tests evaluate the old and the new
 * form in the same SELECT, over the same rows, and require identical results; the old form is
 * kept below as the oracle.
 *
 * Both listings are covered: objects (objects.path/key against plugin_datahub_workspaces_object)
 * and assets (assets.path/filename against plugin_datahub_workspaces_asset). The listed rows live
 * in a temporary table created from the listed table's own columns, so the comparison runs under
 * the real column collations (LOCATE is collation-aware).
 *
 * This is a Model test because that suite runs the bundle installer, which creates the workspace
 * tables.
 */
class WorkspaceReadPermissionConditionTest extends Unit
{
    private const CONFIG = 'workspace-read-permission-condition-test';

    private const PATHS_TABLE = 'datahub_workspace_condition_paths';

    private const FIRST_CID = 990000000;

    /**
     * The listed table and the column holding the element's name, per element type.
     */
    private const LISTED_TABLES = [
        'object' => ['objects', 'key'],
        'asset' => ['assets', 'filename'],
    ];

    private const NESTED_RULES = [['/p', 1], ['/p/q', 0], ['/p/q/r', 1]];

    private const PATTERN_RULES = [['/a/b_c', 1], ['/a/b%d', 1], ['/x\\y', 1], ["/it's", 1]];

    /**
     * Workspaces as cpath and read. Every cpath is also a listed path.
     */
    private const WORKSPACE_SETS = [
        'nested read=0 under read=1' => self::NESTED_RULES,
        'LIKE wildcards, backslash and quote in paths' => self::PATTERN_RULES,
        'read=0 next to LIKE wildcards' => [['/a', 1], ['/a/b_c/d', 0], ['/a/b_c/d/e', 1], ['/a/b%d/e', 1]],
        'case' => [['/C/upper', 1]],
        'longest by bytes, not characters' => [['/ééé', 0], ['/abcde', 1]],
        'read is NULL' => [['/n', null], ['/n/m', 1]],
        'root' => [['/', 1], ['/s', 0]],
        'none' => [],
    ];

    /**
     * Listed paths in addition to the workspace paths: their descendants, string prefixes, and
     * lookalikes that only match if a path were treated as a LIKE pattern or compared
     * case-insensitively.
     *
     * The case lookalikes have a subtree of their own: whether they match depends on how the
     * database compares the paths, and that differs (in CI the MySQL jobs match them
     * case-insensitively, the MariaDB jobs do not). The oracle test holds either way; the fixed
     * expectations below must not depend on it.
     */
    private const OTHER_PATHS = [
        '/a/b', '/a/b_', '/a/b_c/d/e/f', '/a/bxc', '/a/bxc/d', '/a/b%d/e/f', '/a/bzzd/e', '/a/bzzd/e/f',
        '/x', '/x\\y/z', '/xy', "/it's/x", '/C', '/c/upper', '/C/upper/x', '/C/UPPER/x', '/ab', '/abc',
        '/abcde/x', '/q', '/p/q/r/s', '/p/qq', '/p/q/x', '/p/x', '/é', '/ééé/x', '/n/x', '/s/t',
    ];

    protected function _after(): void
    {
        $db = Db::get();
        foreach (array_keys(self::LISTED_TABLES) as $elementType) {
            $db->delete(self::workspacesTable($elementType), ['configuration' => self::CONFIG]);
        }
        $db->executeStatement('DROP TEMPORARY TABLE IF EXISTS ' . self::PATHS_TABLE);
    }

    /**
     * @dataProvider workspaceSets
     */
    public function testMatchesTheCorrelatedSubqueries(string $elementType, array $workspaces): void
    {
        $this->createListedTable($elementType);
        $this->saveWorkspaces($elementType, $workspaces);

        $rows = Db::get()->fetchAllAssociative(
            'SELECT ' . $this->fullPathExpression($elementType) . ' AS fullPath'
            . ', (' . $this->oldCondition($elementType) . ') AS old'
            . ', (' . $this->newCondition($elementType) . ') AS new'
            . ' FROM ' . self::PATHS_TABLE
        );

        $this->assertCount(count(self::listedPaths()), $rows);
        foreach ($rows as $row) {
            // a WHERE clause only sees truthiness: NULL and 0 both exclude the row
            $this->assertSame((int) $row['old'], (int) $row['new'], $row['fullPath']);
        }
    }

    public static function workspaceSets(): array
    {
        $data = [];
        foreach (array_keys(self::LISTED_TABLES) as $elementType) {
            foreach (self::WORKSPACE_SETS as $name => $workspaces) {
                $data[$elementType . ': ' . $name] = [$elementType, $workspaces];
            }
        }

        return $data;
    }

    public static function elementTypes(): array
    {
        $data = [];
        foreach (array_keys(self::LISTED_TABLES) as $elementType) {
            $data[$elementType] = [$elementType];
        }

        return $data;
    }

    /**
     * The semantics spelled out, independent of the oracle: the longest workspace path that the
     * element's path starts with decides (string prefix, so /p/q also covers /p/qq), and an element
     * whose path a workspace path starts with is readable if the longest such workspace is.
     *
     * @dataProvider elementTypes
     */
    public function testNestedDenyRule(string $elementType): void
    {
        $this->createListedTable($elementType);
        $this->saveWorkspaces($elementType, self::NESTED_RULES);

        $this->assertEqualsCanonicalizing(
            ['/', '/p', '/p/q', '/p/q/r', '/p/q/r/s', '/p/x'],
            $this->readablePaths($elementType)
        );
    }

    /**
     * @dataProvider elementTypes
     */
    public function testNoWorkspacesMeansNothingIsReadable(string $elementType): void
    {
        $this->createListedTable($elementType);

        $this->assertSame(' FALSE', $this->newCondition($elementType));
        $this->assertSame([], $this->readablePaths($elementType));
    }

    /**
     * @dataProvider elementTypes
     */
    public function testPathsAreQuotedNotPatternMatched(string $elementType): void
    {
        $this->createListedTable($elementType);
        $this->saveWorkspaces($elementType, self::PATTERN_RULES);

        // /a/bxc, /a/bzzd/e and /xy would match if _, % or \ were treated as LIKE metacharacters;
        // /a/b, /a/b_ and /x are readable because they are string prefixes of a workspace path
        $this->assertEqualsCanonicalizing(
            ['/', '/a', '/a/b', '/a/b_c', '/a/b_c/d', '/a/b_c/d/e', '/a/b_c/d/e/f', '/a/b%d', '/a/b%d/e',
                '/a/b%d/e/f', '/x\\y', '/x\\y/z', '/x', '/a/b_', "/it's", "/it's/x"],
            $this->readablePaths($elementType)
        );
    }

    /**
     * @return string[]
     */
    private static function listedPaths(): array
    {
        $paths = self::OTHER_PATHS;
        foreach (self::WORKSPACE_SETS as $workspaces) {
            foreach ($workspaces as [$cpath]) {
                $paths[] = $cpath;
            }
        }

        return array_values(array_unique($paths));
    }

    /**
     * Creates the listed rows from the listed table's own path and name columns.
     */
    private function createListedTable(string $elementType): void
    {
        [$table, $nameColumn] = self::LISTED_TABLES[$elementType];
        $db = Db::get();
        $name = $db->quoteIdentifier($nameColumn);

        $db->executeStatement('DROP TEMPORARY TABLE IF EXISTS ' . self::PATHS_TABLE);
        $db->executeStatement(sprintf(
            'CREATE TEMPORARY TABLE %s SELECT path, %s FROM %s LIMIT 0',
            self::PATHS_TABLE,
            $name,
            $table
        ));
        foreach (self::listedPaths() as $fullPath) {
            $slash = strrpos($fullPath, '/');
            $db->insert(self::PATHS_TABLE, [
                'path' => substr($fullPath, 0, $slash + 1),
                $name => substr($fullPath, $slash + 1),
            ]);
        }
    }

    /**
     * @return string[]
     */
    private function readablePaths(string $elementType): array
    {
        return Db::get()->fetchFirstColumn(
            'SELECT ' . $this->fullPathExpression($elementType) . ' FROM ' . self::PATHS_TABLE
            . ' WHERE ' . $this->newCondition($elementType)
        );
    }

    /**
     * The listed element's full path, as the listing resolvers build it.
     */
    private function fullPathExpression(string $elementType): string
    {
        $db = Db::get();
        $table = $db->quoteIdentifier(self::PATHS_TABLE);

        return 'CONCAT(' . $table . '.path,' . $table . '.'
            . $db->quoteIdentifier(self::LISTED_TABLES[$elementType][1]) . ')';
    }

    private function newCondition(string $elementType): string
    {
        return WorkspaceHelper::getReadPermissionCondition(
            new Configuration('graphql', null, self::CONFIG),
            $elementType,
            $this->fullPathExpression($elementType)
        );
    }

    /**
     * QueryType::resolveListing()'s and AssetListing::resolveListing()'s condition before
     * getReadPermissionCondition() replaced it. The two differed only in the tables and the
     * column holding the element's name.
     */
    private function oldCondition(string $elementType): string
    {
        $db = Db::get();
        $workspacesTable = $db->quoteIdentifier(self::workspacesTable($elementType));
        $fullPath = $this->fullPathExpression($elementType);
        $lookup = static fn (string $locate): string => '(
                SELECT `read` from ' . $workspacesTable . '
                WHERE ' . $workspacesTable . '.configuration = ' . $db->quote(self::CONFIG) . '
                AND ' . $locate . '=1
                ORDER BY LENGTH(' . $workspacesTable . '.cpath) DESC
                LIMIT 1
            )=1';

        return ' (
            ' . $lookup('LOCATE(' . $fullPath . ',' . $workspacesTable . '.cpath)') . '
            OR
            ' . $lookup('LOCATE(' . $workspacesTable . '.cpath,' . $fullPath . ')') . '
            )';
    }

    private static function workspacesTable(string $elementType): string
    {
        return 'plugin_datahub_workspaces_' . $elementType;
    }

    /**
     * @param array<array{0: string, 1: int|null}> $workspaces cpath and read
     */
    private function saveWorkspaces(string $elementType, array $workspaces): void
    {
        $db = Db::get();
        foreach (array_values($workspaces) as $i => [$cpath, $read]) {
            $db->insert(self::workspacesTable($elementType), [
                'cid' => self::FIRST_CID + $i,
                'cpath' => $cpath,
                'configuration' => self::CONFIG,
                '`read`' => $read,
            ]);
        }
    }
}
