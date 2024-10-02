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

use Pimcore\Bundle\DataHubBundle\Configuration\Workspace\Dao;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\PermissionEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\PermissionEvents;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\NotAllowedException;
use Pimcore\Cache;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Db;
use Pimcore\Model\DataObject\OwnerAwareFieldInterface;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;
use Symfony\Component\EventDispatcher\EventDispatcher;

class WorkspaceHelper
{
    const MODIFY_SPACE_OBJECT = 'object';

    const MODIFY_SPACE_ASSET = 'asset';

    const MODIFY_SPACE_DOCUMENT = 'document';

    const MODIFY_TYPE_REPLACE = 'replace';

    const MODIFY_TYPE_DELETE = 'delete';

    /**
     *
     * @return array
     */
    public static function cleanupWorkspaces(array $workspaces)
    {
        foreach ($workspaces as $type => &$spaces) {
            if (!is_array($spaces)) {
                continue;
            }

            foreach ($spaces as $spaceIndex => $space) {
                $element = Service::getElementByPath($type, $space['cpath']);
                if ($element instanceof ElementInterface) {
                    continue;
                }

                unset($spaces[$spaceIndex]);
            }

            $spaces = array_values($spaces); // reset array keys
        }

        return $workspaces;
    }

    /**
     * @param string        $spaceType
     * @param string        $modificationType
     * @param string        $searchValue
     * @param string|null   $replaceValue
     *
     * @return Configuration|void
     */
    public static function modifyWorkspaceRowByType(Configuration $configurationEntity, $spaceType, $modificationType, $searchValue, $replaceValue)
    {
        $changed = false;

        $configuration = $configurationEntity->getConfiguration();
        if (!isset($configuration['workspaces']) || !is_array($configuration['workspaces'])) {
            return;
        }

        $workspaces = $configuration['workspaces'];
        if (!isset($workspaces[$spaceType])) {
            return;
        }

        $spaces = $workspaces[$spaceType];
        if (!is_array($spaces)) {
            return;
        }

        foreach ($spaces as $spaceIndex => &$space) {
            if (!isset($space['cpath'])) {
                continue;
            }

            $cPath = $space['cpath'];
            $cTrailingPath = sprintf('%s/', $space['cpath']);
            $cTrailingSearchValue = sprintf('%s/', $searchValue);
            $cTrailingReplaceValue = sprintf('%s/', $replaceValue);

            if ($cPath === $searchValue) {

                // it's the element itself
                $changed = true;

                if ($modificationType === self::MODIFY_TYPE_REPLACE) {
                    $space['cpath'] = $replaceValue;
                } elseif ($modificationType === self::MODIFY_TYPE_DELETE) {
                    unset($spaces[$spaceIndex]);
                    $spaces = array_values($spaces); // reset array keys
                }
            } elseif (strpos($cTrailingPath, $cTrailingSearchValue) !== false) {

                // it's a sub element
                $changed = true;

                if ($modificationType === self::MODIFY_TYPE_REPLACE) {
                    $space['cpath'] = str_replace($cTrailingSearchValue, $cTrailingReplaceValue, $space['cpath']);
                } elseif ($modificationType === self::MODIFY_TYPE_DELETE) {
                    unset($spaces[$spaceIndex]);
                    $spaces = array_values($spaces); // reset array keys
                }
            }
        }

        if ($changed === false) {
            return;
        }

        $workspaces[$spaceType] = $spaces;
        $configuration['workspaces'] = $workspaces;

        $configurationEntity->setConfiguration($configuration);

        return $configurationEntity;
    }

    /**
     * @param array         $workspaces
     *
     * @throws \Exception
     */
    public static function saveWorkspaces(Configuration $config, $workspaces)
    {
        $db = Db::get();
        $db->delete(Dao::TABLE_NAME_DOCUMENT, ['configuration' => $config->getName()]);
        $db->delete(Dao::TABLE_NAME_ASSET, ['configuration' => $config->getName()]);
        $db->delete(Dao::TABLE_NAME_DATAOBJECT, ['configuration' => $config->getName()]);

        if (is_array($workspaces)) {
            foreach ($workspaces as $type => $spaces) {
                foreach ($spaces as $space) {
                    $element = \Pimcore\Model\Element\Service::getElementByPath($type, $space['cpath']);
                    if ($element) {
                        $className = '\\Pimcore\\Bundle\\DataHubBundle\\Configuration\\Workspace\\' . \Pimcore\Model\Element\Service::getBaseClassNameForElement($type);
                        /** @var Configuration\Workspace\AbstractWorkspace $workspace */
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
    }

    /**
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function loadWorkspaces(Configuration $configuration)
    {
        $workspaces = [];
        $types = ['document', 'asset', 'object'];
        $db = Db::get();

        foreach ($types as $type) {
            $workspaces[$type] = [];

            $className = '\\Pimcore\\Bundle\\DataHubBundle\\Configuration\\Workspace\\' . \Pimcore\Model\Element\Service::getBaseClassNameForElement($type);
            $result = $db->fetchAllAssociative('SELECT * FROM plugin_datahub_workspaces_' . $type . ' WHERE configuration = ?', [$configuration->getName()]);
            foreach ($result as $row) {
                $workspace = new $className();
                $workspace->setValues($row);
                $workspaces[$type][] = $workspace;
            }
        }

        return $workspaces;
    }

    /**
     *
     * @throws \Exception
     */
    public static function deleteConfiguration(Configuration $config)
    {
        $db = Db::get();

        $db->delete(Dao::TABLE_NAME_DOCUMENT, ['configuration' => $config->getName()]);
        $db->delete(Dao::TABLE_NAME_ASSET, ['configuration' => $config->getName()]);
        $db->delete(Dao::TABLE_NAME_DATAOBJECT, ['configuration' => $config->getName()]);
    }

    /**
     * @param ElementInterface|OwnerAwareFieldInterface $element
     * @param string $type
     *
     * @return bool
     *
     * @throws NotAllowedException
     */
    public static function checkPermission($element, $type)
    {
        $context = RuntimeCache::get('datahub_context');
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];

        if ($configuration->skipPermisssionCheck()) {
            return true;
        }

        $event = new PermissionEvent($element, $type);
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = \Pimcore::getContainer()->get('event_dispatcher');
        $eventDispatcher->dispatch($event, PermissionEvents::PRE_CHECK);
        if (!$event->isGranted() && PimcoreDataHubBundle::getNotAllowedPolicy() === PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION) {
            throw new ClientSafeException('access for '.  $element->getFullPath() . ' denied');
        }

        $isAllowed = self::isAllowed($element, $configuration, $type);
        if (!$isAllowed && PimcoreDataHubBundle::getNotAllowedPolicy() === PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION) {
            $elementType = Service::getElementType($element);

            throw new ClientSafeException($type . ' access for ' . $elementType . ' ' . $element->getFullPath() . ' denied');
        }

        return $isAllowed;
    }

    /**
     * @internal
     *
     * @param ElementInterface|OwnerAwareFieldInterface|null $element
     *
     * @return bool
     */
    public static function isAllowed($element, Configuration $configuration, string $type)
    {
        if (!$element) {
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
        if ($element->getId()) {
            $parentIds[] = $element->getId();
        }

        $lookupTable = self::fetchLookupTable($elementType, $configuration);
        foreach ($parentIds as $parentId) {
            if (isset($lookupTable[$parentId]) && $lookupTable[$parentId][$type] === 1) {
                return true;
            }
        }

        if ($type === 'read') {
            $path = $element->getRealFullPath() . '/';
            $path = str_replace('_', '\\_', $path);
            if ($element->getId() === 1) {
                $path = '/';
            }

            foreach ($lookupTable as $row) {
                if (strpos($row['cpath'], $path) === 0 && $row[$type] == 1) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function fetchLookupTable(string $elementType, Configuration $configuration): array
    {
        $cacheKey = 'datahub_permissions_' . $configuration->getName() . '_' . $elementType;
        if (RuntimeCache::isRegistered($cacheKey)) {
            return RuntimeCache::load($cacheKey);
        }

        $cache = Cache::load($cacheKey);
        if ($cache !== false) {
            RuntimeCache::save($cache, $cacheKey);

            return $cache;
        }

        $db = Db::get();
        $sql = 'SELECT cid, cpath, `create`, `read`, `update`, `delete` FROM plugin_datahub_workspaces_' . $elementType . ' WHERE configuration = ?';
        $rows = $db->fetchAllAssociative($sql, [$configuration->getName()]);

        $lookupTable = [];
        foreach ($rows as $row) {
            $lookupTable[$row['cid']] = $row;
        }

        RuntimeCache::save($lookupTable, $cacheKey);
        Cache::save($lookupTable, $cacheKey);

        return $lookupTable;
    }
}
