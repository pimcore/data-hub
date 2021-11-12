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

namespace Pimcore\Bundle\DataHubBundle\Helper;

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Configuration\Permission;
use Pimcore\Db;
use Pimcore\Logger;
use Pimcore\Model\User;
use Pimcore\Tool\Admin;

class PermissionsHelper
{
    const TABLE_NAME = 'plugin_datahub_permissions';

    /**
     * @param Configuration $config
     * @param array $permissions
     *
     * @throws \Exception
     */
    public static function savePermissions(Configuration $config, $permissions)
    {
        $db = Db::get();
        $db->delete(Permission\Dao::TABLE_NAME, ['configuration' => $config->getName()]);

        if (is_array($permissions)) {
            foreach ($permissions as $userType =>  $userPermissions) {
                foreach ($userPermissions as $userPermission) {
                    $user = User\AbstractUser::getById($userPermission['id']);

                    if ($user) {
                        $permission = new Permission();
                        $permission->setValues($userPermission);
                        $permission->setConfiguration($config->getName());
                        $permission->setUid($userPermission['id']);
                        $permission->setUname($userPermission['name']);
                        $permission->setUtype($userType);
                        $permission->setType($config->getType());
                        $permission->save();
                    }
                }
            }
        }
    }

    /**
     * @param Configuration $configuration
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function loadPermissions(Configuration $configuration)
    {
        $permissions = [];

        $db = Db::get();
        $userTypes = ['user', 'role'];
        foreach ($userTypes as $userType) {
            $result = $db->fetchAll('SELECT * FROM ? WHERE configuration = ?', [self::TABLE_NAME, $configuration->getName()]);
            foreach ($result as $row) {
                $permission = new Permission();
                $permission->setValues($row);
                $permissions[$userType][] = $permission;
            }
        }

        return $permissions;
    }

    /**
     * @internal
     *
     * @param Configuration $configuration
     * @param string $type
     * @param ?User $user
     *
     * @return bool
     */
    public static function isAllowed(Configuration $configuration, string $type, ?User $user = null)
    {
        if (null === $user) {
            $user = Admin::getCurrentUser();
        }

        if (!$user) {
            if (php_sapi_name() === 'cli') {
                return true;
            }

            return false;
        }

        //everything is allowed for admin
        if ($user->isAdmin()) {
            return true;
        }

        $configKey = "plugin_datahub_adapter_" . $configuration->getType();
        if ($user->isAllowed($configKey)) {
            return true;
        }

        $userRoles = $user->getRoles();
        foreach ($userRoles as $userRoleId) {
            $role = User\Role::getById($userRoleId);
            if ($role->getPermission($configKey)) {
                return true;
            }
        }

        $userIds = array_merge([$user->getId()], $user->getRoles());

        try {
            $db = Db::get();
            $sql = 'SELECT `' . $type . '` FROM ' . self::TABLE_NAME . ' WHERE uid IN (' . implode(',', $userIds) . ') AND configuration = ' . $db->quote($configuration->getName()) . ' AND `' . $type . '`=1 ORDER BY LENGTH(utype) DESC LIMIT 1';
            $allowedPermission = $db->fetchOne($sql);

            if ($allowedPermission) {
                return true;
            }
        } catch (\Exception $e) {
            Logger::warn('Unable to get permission ' . $type . ' for user:' . $user->getName() . ' for configuration:' . $configuration->getName());
        }

        return false;
    }
}
