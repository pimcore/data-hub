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
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle;

use Pimcore\Db;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Logger;

class Installer extends AbstractInstaller
{
    /**
     * {@inheritdoc}
     */
    public function isInstalled()
    {
        $db = Db::get();
        $check = $db->fetchRow("SELECT * FROM users_permission_definitions where `key`='plugin_datahub_config'");
        if (!$check) {
            return false;
        }

        return true;
    }

    public function needsReloadAfterInstall()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function canBeInstalled()
    {
        return !$this->isInstalled();
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        // create backend permission
        \Pimcore\Model\User\Permission\Definition::create('plugin_datahub_config');

        try {
            $db = Db::get();

            $db->query("
                CREATE TABLE IF NOT EXISTS `plugin_datahub_workspaces_asset` (
                    `cid` INT(11) UNSIGNED NOT NULL DEFAULT '0',
                    `cpath` VARCHAR(765) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
                    `configuration` VARCHAR(50) NOT NULL DEFAULT '0',
                    `read` TINYINT(1) UNSIGNED NULL DEFAULT '0',
                    `write` TINYINT(1) UNSIGNED NULL DEFAULT '0',
                    PRIMARY KEY (`cid`, `configuration`)                
                    )
                COLLATE='utf8mb4_general_ci'
                ENGINE=InnoDB
                ;                        
            ");

            $db->query("
                CREATE TABLE IF NOT EXISTS `plugin_datahub_workspaces_object` (
                    `cid` INT(11) UNSIGNED NOT NULL DEFAULT '0',
                    `cpath` VARCHAR(765) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
                    `configuration` VARCHAR(50) NOT NULL DEFAULT '0',
                    `read` TINYINT(1) UNSIGNED NULL DEFAULT '0',
                    `write` TINYINT(1) UNSIGNED NULL DEFAULT '0',
                    PRIMARY KEY (`cid`, `configuration`)                
                    )
                COLLATE='utf8mb4_general_ci'
                ENGINE=InnoDB
                ;                        
            ");
        } catch (\Exception $e) {
            Logger::warn($e);
        }

        return true;
    }
}
