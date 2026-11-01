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

namespace Pimcore\Bundle\DataHubBundle\Tests\Service;

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
 * The listed rows live in a temporary table created from objects' own path/key columns, so the
 * comparison runs under the real column collations (LOCATE is collation-aware).
 */
class WorkspaceReadPermissionConditionTest extends Unit
{
    private const CONFIG = 'workspace-read-permission-condition-test';
    private const PATHS_TABLE = 'datahub_workspace_condition_paths';
    private const FIRST_CID = 990000000;

    private const PATHS = [
        '/', '/a', '/a/b', '/a/b_c', '/a/b_c/d', '/a/b_c/d/e', '/a/b_c/d/e/f', '/a/bxc', '/a/bxc/d',
        '/a/b%d', '/a/b%d/e', '/a/b%d/e/f', '/a/bzzd/e', '/a/bzzd/e/f', '/x\\y', '/x\\y/z', '/xy', '/x',
        '/A', '/a/upper', '/A/upper/x', '/A/UPPER/x', '/ab', '/q', '/a/b_', "/it's", "/it's/x",
        '/p', '/p/q', '/p/q/r', '/p/q/r/s', '/p/qq', '/p/q/x', '/p/x',
        '/é', '/ééé/x', '/abcde/x', '/abc', '/n', '/n/m', '/n/x', '/s', '/s/t',
    ];

    protected function _before(): void
    {
        $db = Db::get();
        $this->deleteWorkspaces();
        $db->executeStatement('DROP TEMPORARY TABLE IF EXISTS ' . self::PATHS_TABLE);
        $db->executeStatement('CREATE TEMPORARY TABLE ' . self::PATHS_TABLE . ' SELECT path, `key` FROM objects LIMIT 0');
        foreach (self::PATHS as $fullPath) {
            $slash = strrpos($fullPath, '/');
            $db->insert(self::PATHS_TABLE, [
                'path' => $fullPath === '/' ? '/' : substr($fullPath, 0, $slash + 1),
                '`key`' => $fullPath === '/' ? '' : substr($fullPath, $slash + 1),
            ]);
        }
    }

    protected function _after(): void
    {
        $this->deleteWorkspaces();
        Db::get()->executeStatement('DROP TEMPORARY TABLE IF EXISTS ' . self::PATHS_TABLE);
    }

    /**
     * @dataProvider workspaceSets
     */
    public function testMatchesTheCorrelatedSubqueries(array $workspaces): void
    {
        $this->saveWorkspaces($workspaces);
        $db = Db::get();
        $table = $db->quoteIdentifier(self::PATHS_TABLE);

        $rows = $db->fetchAllAssociative(
            'SELECT CONCAT(path, `key`) AS fullPath, (' . $this->oldCondition() . ') AS old, (' . $this->newCondition() . ') AS new FROM ' . $table
        );

        $this->assertCount(count(self::PATHS), $rows);
        foreach ($rows as $row) {
            // a WHERE clause only sees truthiness: NULL and 0 both exclude the row
            $this->assertSame((int) $row['old'], (int) $row['new'], $row['fullPath']);
        }
    }

    public static function workspaceSets(): array
    {
        return [
            'nested read=0 under read=1, LIKE wildcards, backslash, quote, case' => [[
                ['/a', 1], ['/a/b_c', 0], ['/a/b_c/d/e', 1], ['/a/b%d/e', 1], ['/x\\y', 1], ['/A/upper', 1], ["/it's", 1],
            ]],
            'three levels alternating' => [[['/p', 1], ['/p/q', 0], ['/p/q/r', 1]]],
            'longest by bytes, not characters' => [[['/ééé', 0], ['/abcde', 1]]],
            'read is NULL' => [[['/n', null], ['/n/m', 1]]],
            'root' => [[['/', 1], ['/s', 0]]],
            'none' => [[]],
        ];
    }

    /**
     * The semantics spelled out, independent of the oracle: the longest workspace path that the
     * element's path starts with decides (string prefix, so /p/q also covers /p/qq), and an element
     * whose path a workspace path starts with is readable if the longest such workspace is.
     */
    public function testNestedDenyRule(): void
    {
        $this->saveWorkspaces([['/p', 1], ['/p/q', 0], ['/p/q/r', 1]]);

        $this->assertSame(
            ['/', '/p', '/p/q', '/p/q/r', '/p/q/r/s', '/p/x'],
            $this->readablePaths()
        );
    }

    public function testNoWorkspacesMeansNothingIsReadable(): void
    {
        $this->assertSame(' FALSE', $this->newCondition());
        $this->assertSame([], $this->readablePaths());
    }

    public function testPathsAreQuotedNotPatternMatched(): void
    {
        $this->saveWorkspaces([['/a/b_c', 1], ['/a/b%d', 1], ['/x\\y', 1], ["/it's", 1]]);

        // /a/bxc, /a/bzzd/e and /xy would match if _, % or \ were treated as LIKE metacharacters;
        // /a/b, /a/b_ and /x are readable because they are string prefixes of a workspace path
        $this->assertSame(
            ['/', '/a', '/a/b', '/a/b_c', '/a/b_c/d', '/a/b_c/d/e', '/a/b_c/d/e/f', '/a/b%d', '/a/b%d/e', '/a/b%d/e/f',
                '/x\\y', '/x\\y/z', '/x', '/a/b_', "/it's", "/it's/x"],
            $this->readablePaths()
        );
    }

    /**
     * @return string[] in the order of self::PATHS
     */
    private function readablePaths(): array
    {
        $db = Db::get();

        $paths = $db->fetchFirstColumn(
            'SELECT CONCAT(path, `key`) FROM ' . $db->quoteIdentifier(self::PATHS_TABLE) . ' WHERE ' . $this->newCondition()
        );

        return array_values(array_intersect(self::PATHS, $paths));
    }

    private function newCondition(): string
    {
        $db = Db::get();
        $table = $db->quoteIdentifier(self::PATHS_TABLE);

        return WorkspaceHelper::getReadPermissionCondition(
            new Configuration('graphql', null, self::CONFIG),
            'object',
            'CONCAT(' . $table . '.path,' . $table . '.key)'
        );
    }

    /**
     * QueryType::resolveListing()'s condition before getReadPermissionCondition() replaced it.
     */
    private function oldCondition(): string
    {
        $db = Db::get();
        $tableName = self::PATHS_TABLE;
        $workspacesTableName = 'plugin_datahub_workspaces_object';

        return ' (
            (
                SELECT `read` from ' . $db->quoteIdentifier($workspacesTableName) . '
                WHERE ' . $db->quoteIdentifier($workspacesTableName) . '.configuration = ' . $db->quote(self::CONFIG) . '
                AND LOCATE(CONCAT(' . $db->quoteIdentifier($tableName) . '.path,' . $db->quoteIdentifier($tableName) . '.key),' . $db->quoteIdentifier($workspacesTableName) . '.cpath)=1
                ORDER BY LENGTH(' . $db->quoteIdentifier($workspacesTableName) . '.cpath) DESC
                LIMIT 1
            )=1
            OR
            (
                SELECT `read` from ' . $db->quoteIdentifier($workspacesTableName) . '
                WHERE ' . $db->quoteIdentifier($workspacesTableName) . '.configuration = ' . $db->quote(self::CONFIG) . '
                AND LOCATE(' . $db->quoteIdentifier($workspacesTableName) . '.cpath,CONCAT(' . $db->quoteIdentifier($tableName) . '.path,' . $db->quoteIdentifier($tableName) . '.key))=1
                ORDER BY LENGTH(' . $db->quoteIdentifier($workspacesTableName) . '.cpath) DESC
                LIMIT 1
            )=1
            )';
    }

    /**
     * @param array<array{0: string, 1: int|null}> $workspaces cpath and read
     */
    private function saveWorkspaces(array $workspaces): void
    {
        $db = Db::get();
        foreach (array_values($workspaces) as $i => [$cpath, $read]) {
            $db->insert('plugin_datahub_workspaces_object', [
                'cid' => self::FIRST_CID + $i,
                'cpath' => $cpath,
                'configuration' => self::CONFIG,
                '`read`' => $read,
            ]);
        }
    }

    private function deleteWorkspaces(): void
    {
        Db::get()->delete('plugin_datahub_workspaces_object', ['configuration' => self::CONFIG]);
    }
}
