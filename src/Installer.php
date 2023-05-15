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

namespace Pimcore\Bundle\DataHubBundle;

use Pimcore\Bundle\DataHubBundle\Controller\ConfigController;
use Pimcore\Bundle\DataHubBundle\Migrations\PimcoreX\Version20230503165847;
use Pimcore\Db;
use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Pimcore\Logger;
use Pimcore\Model\Tool\SettingsStore;
use Pimcore\Model\User\Permission\Definition;

class Installer extends SettingsStoreAwareInstaller
{
    const DATAHUB_PERMISSION_CATEGORY = 'Datahub';
    const DATAHUB_ADAPTER_PERMISSION = 'plugin_datahub_adapter_graphql';
    const DATAHUB_ADMIN_PERMISSION = 'plugin_datahub_admin';

    public function needsReloadAfterInstall(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function install(): void
    {
        try {
            // create backend permission
            Definition::create(ConfigController::CONFIG_NAME)->setCategory(self::DATAHUB_PERMISSION_CATEGORY)->save();
            Definition::create(self::DATAHUB_ADAPTER_PERMISSION)->setCategory(self::DATAHUB_PERMISSION_CATEGORY)->save();
            Definition::create(self::DATAHUB_ADMIN_PERMISSION)->setCategory(self::DATAHUB_PERMISSION_CATEGORY)->save();

            $types = ['document', 'asset', 'object'];

            $db = Db::get();
            foreach ($types as $type) {
                $db->executeQuery('
                    CREATE TABLE IF NOT EXISTS `plugin_datahub_workspaces_' . $type . "` (
                        `cid` INT(11) UNSIGNED NOT NULL DEFAULT '0',
                        `cpath` VARCHAR(765) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
                        `configuration` VARCHAR(80) NOT NULL DEFAULT '0',
                        `create` TINYINT(1) UNSIGNED NULL DEFAULT '0',
                        `read` TINYINT(1) UNSIGNED NULL DEFAULT '0',
                        `update` TINYINT(1) UNSIGNED NULL DEFAULT '0',
                        `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0',
                        PRIMARY KEY (`cid`, `configuration`)
                        )
                    COLLATE='utf8mb4_general_ci'
                    ENGINE=InnoDB
                    ;
                ");
            }
        } catch (\Exception $e) {
            Logger::warn($e);
            throw new InstallationException($e->getMessage());
        }

        parent::install();
    }

    public function isInstalled(): bool
    {
        // When switching to SettingsStoreAwareInstaller, we need to explicitly mark this bundle installed, if Settingstore entry doesn't exists and datahub permission is installed
        // e.g. updating from 1.0.* to 1.1.*
        $installEntry = SettingsStore::get($this->getSettingsStoreInstallationId(), 'pimcore');
        if (!$installEntry) {
            $db = Db::get();
            $check = $db->fetchOne('SELECT `key` FROM users_permission_definitions where `key` = ?', [ConfigController::CONFIG_NAME]);
            if ($check) {
                SettingsStore::set('BUNDLE_INSTALLED__Pimcore\\Bundle\\DataHubBundle\\PimcoreDataHubBundle', true, 'bool', 'pimcore');

                return true;
            }
        }

        return parent::isInstalled();
    }

    public function getLastMigrationVersionClassName(): ?string
    {
        return Version20230503165847::class;
    }
}
