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

namespace Pimcore\Bundle\DataHubBundle\Migrations\Pimcore2026;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @internal
 */
final class Version20260506124014 extends AbstractMigration
{
    private const KEYS = [
        'plugin_datahub_config',
        'plugin_datahub_adapter_graphql',
        'plugin_datahub_admin',
    ];

    public function getDescription(): string
    {
        return 'Migrate data-hub backend translations from deprecated admin domain to backend domain';
    }

    public function up(Schema $schema): void
    {
        if (empty($this->connection->fetchAllAssociative("SHOW TABLES LIKE 'translations_admin'"))) {
            $this->write('Skipping migration: translations_admin table does not exist.');

            return;
        }

        $inList = "'" . implode("', '", self::KEYS) . "'";

        $this->addSql(
            "INSERT IGNORE INTO `translations_backend` (`key`, `type`, `language`, `text`, `creationDate`, `modificationDate`, `userOwner`, `userModification`)
             SELECT `key`, `type`, `language`, `text`, `creationDate`, `modificationDate`, `userOwner`, `userModification`
             FROM `translations_admin`
             WHERE `key` IN ($inList)"
        );

        $this->addSql(
            "UPDATE `translations_backend` tb
             INNER JOIN `translations_admin` ta ON ta.`key` = tb.`key` AND ta.`language` = tb.`language`
             SET tb.`text` = ta.`text`, tb.`type` = ta.`type`
             WHERE tb.`key` IN ($inList)
               AND (tb.`text` IS NULL OR tb.`text` = '')"
        );

        $this->addSql("DELETE FROM `translations_admin` WHERE `key` IN ($inList)");
    }

    public function down(Schema $schema): void
    {
        if (empty($this->connection->fetchAllAssociative("SHOW TABLES LIKE 'translations_admin'"))) {
            $this->write('Skipping revert: translations_admin table does not exist.');

            return;
        }

        $inList = "'" . implode("', '", self::KEYS) . "'";

        $this->addSql(
            "INSERT IGNORE INTO `translations_admin` (`key`, `type`, `language`, `text`, `creationDate`, `modificationDate`, `userOwner`, `userModification`)
             SELECT `key`, `type`, `language`, `text`, `creationDate`, `modificationDate`, `userOwner`, `userModification`
             FROM `translations_backend`
             WHERE `key` IN ($inList)"
        );

        $this->addSql(
            "UPDATE `translations_admin` ta
             INNER JOIN `translations_backend` tb ON tb.`key` = ta.`key` AND tb.`language` = ta.`language`
             SET ta.`text` = tb.`text`, ta.`type` = tb.`type`
             WHERE ta.`key` IN ($inList)
               AND (ta.`text` IS NULL OR ta.`text` = '')"
        );

        $this->addSql("DELETE FROM `translations_backend` WHERE `key` IN ($inList)");
    }
}
