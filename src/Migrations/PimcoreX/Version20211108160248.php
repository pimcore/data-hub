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

namespace Pimcore\Bundle\DataHubBundle\Migrations\PimcoreX;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Bundle\DataHubBundle\Controller\ConfigController;
use Pimcore\Bundle\DataHubBundle\Installer;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211108160248 extends AbstractMigration
{
    private function migrateUsers(bool $up) {
        $listing = new \Pimcore\Model\User\Listing();
        $listing->setCondition('`type` = ? or `type` = ?', ['role', 'user']);
        $listing->load();
        $list = $listing->getItems();

        foreach($list as $item) {
            $permissions = $item->getPermissions();
            if(($up === true && in_array( ConfigController::CONFIG_NAME, $permissions) && !in_array(Installer::DATAHUB_ADMIN_PERMISSION, $permissions)) ||
                ($up === false && in_array(Installer::DATAHUB_ADMIN_PERMISSION, $permissions))) {
                if($up === true) {
                    $permissions[] = Installer::DATAHUB_ADMIN_PERMISSION;
                } else {
                    array_splice($permissions, array_search(Installer::DATAHUB_ADMIN_PERMISSION, $permissions));
                }
                $item->setPermissions($permissions);
                $item->save();
            }
        }
    }
    public function up(Schema $schema): void
    {
        $this->addSql(sprintf("INSERT IGNORE INTO users_permission_definitions (`key`) VALUES('%s');", Installer::DATAHUB_ADAPTER_PERMISSION));
        $this->addSql(sprintf("INSERT IGNORE INTO users_permission_definitions (`key`) VALUES('%s');", Installer::DATAHUB_ADMIN_PERMISSION));
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

        $this->migrateUsers(true);
    }

    public function down(Schema $schema): void
    {
        $this->migrateUsers(false);
        $this->addSql(sprintf("DELETE FROM users_permission_definitions WHERE `key` = '%s'", Installer::DATAHUB_ADAPTER_PERMISSION));
        $this->addSql(sprintf("DELETE FROM users_permission_definitions WHERE `key` = '%s'", Installer::DATAHUB_ADMIN_PERMISSION));
        $this->addSql('DROP TABLE IF EXISTS `plugin_datahub_permissions`');
    }
}
