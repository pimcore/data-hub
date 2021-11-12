<?php

declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\Migrations\PimcoreX;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Bundle\DataHubBundle\Installer;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211108160248 extends AbstractMigration
{

    public function up(Schema $schema): void
    {
        $this->addSql(sprintf("INSERT IGNORE INTO users_permission_definitions (`key`) VALUES('%s');", Installer::DATAHUB_ADAPTER_PERMISSION));
        $this->addSql("CREATE TABLE IF NOT EXISTS `plugin_datahub_permissions` (
            `uid` INT(11) UNSIGNED NOT NULL DEFAULT '0',
            `uname` VARCHAR(765) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
            `utype` ENUM('user','role') NOT NULL DEFAULT 'user',
            `configuration` VARCHAR(50) NOT NULL DEFAULT '0',
            `type` VARCHAR(50) NOT NULL DEFAULT '0',
            `read` TINYINT(1) UNSIGNED NULL DEFAULT '0',
            `update` TINYINT(1) UNSIGNED NULL DEFAULT '0',
            `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0',
            PRIMARY KEY (`uid`, `configuration`)
            )
        COLLATE='utf8mb4_general_ci'
        ENGINE=InnoDB
        ;");
    }

    public function down(Schema $schema): void
    {
        $this->addSql(sprintf("DELETE FROM users_permission_definitions WHERE `key` = '%s'", Installer::DATAHUB_ADAPTER_PERMISSION));
        $this->addSql("DROP DATABASE IF EXISTS `plugin_datahub_permissions`");
    }
}
