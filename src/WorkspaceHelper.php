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

use Pimcore\Bundle\DataHubBundle\Configuration\Workspace\Dao;
use Pimcore\Bundle\DataHubBundle\Configuration\Workspace\Document;
use Pimcore\Db;
use Pimcore\Logger;
use Pimcore\Model\DataObject\OwnerAwareFieldInterface;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;

class WorkspaceHelper
{
    /**
     * @param Configuration $config
     * @param $workspaces
     *
     * @throws \Exception
     */
    public static function saveWorkspaces(Configuration $config, $workspaces)
    {
        $db = Db::get();
        $db->delete(Dao::TABLE_NAME_ASSET, ['configuration' => $config->getName()]);
        $db->delete(Dao::TABLE_NAME_DATAOBJECT, ['configuration' => $config->getName()]);

        foreach ($workspaces as $type => $spaces) {
            foreach ($spaces as $space) {
                $element = \Pimcore\Model\Element\Service::getElementByPath($type, $space['cpath']);
                if ($element) {
                    $className = '\\Pimcore\\Bundle\\DataHubBundle\\Configuration\\Workspace\\' . \Pimcore\Model\Element\Service::getBaseClassNameForElement($type);
                    /** @var $workspace Configuration\Workspace\AbstractWorkspace */
                    $workspace = new $className();
                    $workspace->setValues($space);

                    $workspace->setConfiguration($config->getName());
                    $workspace->setCid($element->getId());
                    $workspace->setCpath($element->getRealFullPath());
                    $workspace->save();
                }
            }
        }
    }

    /**
     * @param Configuration $configuration
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function loadWorkspaces(Configuration $configuration)
    {
        $types = ['asset', 'object'];
        $db = Db::get();

        foreach ($types as $type) {
            $workspaces[$type] = [];

            $className = '\\Pimcore\\Bundle\\DataHubBundle\\Configuration\\Workspace\\' . \Pimcore\Model\Element\Service::getBaseClassNameForElement($type);
            $result = $db->fetchAll('SELECT * FROM plugin_datahub_workspaces_' . $type . ' WHERE configuration = ?', [$configuration->getName()]);
            foreach ($result as $row) {
                $workspace = new $className();
                $workspace->setValues($row);
                $workspaces[$type][] = $workspace;
            }
        }

        return $workspaces;
    }

    /**
     * @param Configuration $config
     *
     * @throws \Exception
     */
    public static function deleteConfiguration(Configuration $config)
    {
        $db = Db::get();
        $db->delete(Dao::TABLE_NAME_DATAOBJECT, ['configuration' => $config->getName()]);
        $db->delete(Dao::TABLE_NAME_ASSET, ['configuration' => $config->getName()]);
    }

    /**
     * @param ElementInterface|OwnerAwareFieldInterface $element
     * @param Configuration $configuration
     * @param string $type
     *
     * @return bool
     */
    public static function isAllowed($element, Configuration $configuration, string $type)
    {
        if (!$element) {
            return false;
        }

        if ($element instanceof Document) {
            // no support for documents right now
            return false;
        }

        $elementType = Service::getElementType($element);
        // collect properties via parent - ids
        $parentIds = [1];

        $parent = $element->getParent();
        if ($parent) {
            while ($parent) {
                $parentIds[] = $parent->getId();
                $parent = $parent->getParent();
            }
        }
        $parentIds[] = $element->getId();

        try {
            $db = Db::get();
            $sql = 'SELECT `' . $type . '` FROM plugin_datahub_workspaces_' . $elementType . ' WHERE cid IN (' . implode(',', $parentIds) . ') AND configuration = ' . $db->quote($configuration->getName()) . ' AND `' . $type . '`=1 ORDER BY LENGTH(cpath) DESC LIMIT 1';
            $permissionsParent = $db->fetchOne($sql);

            if ($permissionsParent) {
                return true;
            }

            // exception for read permission
            if (empty($permissionsParent) && $type == 'read') {
                // check for childs with permissions
                $path = $element->getRealFullPath() . '/';
                if ($element->getId() == 1) {
                    $path = '/';
                }

                $permissionsChilds = $db->fetchOne('SELECT ' . $type . ' FROM plugin_datahub_workspaces_' . $elementType . ' WHERE cpath LIKE ? AND configuration = ' . $db->quote($configuration->getName()). ' AND ' . $type . ' = 1 LIMIT 1', $path . '%');
                if ($permissionsChilds) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            Logger::warn('Unable to get permission ' . $type . ' for ' . $elementType . ' '.  $element->getId());
        }

        return false;
    }
}
