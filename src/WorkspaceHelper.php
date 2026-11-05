<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
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
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\OwnerAwareFieldInterface;
use Pimcore\Model\Document;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class WorkspaceHelper
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
            } elseif (str_starts_with($cTrailingPath, $cTrailingSearchValue)) {

                // it's a sub element
                $changed = true;

                if ($modificationType === self::MODIFY_TYPE_REPLACE) {
                    // only the leading path of the element changed, repeated segments further down stay as they are
                    $space['cpath'] = $cTrailingReplaceValue . substr($space['cpath'], strlen($cTrailingSearchValue));
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
        Cache::clearTags(self::getCacheTags($config));
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

        Cache::clearTags(self::getCacheTags($config));
    }

    /**
     * A folder is only readable if the read setting of its folder type (object_folder / document_folder)
     * is enabled in the schema of the configuration. This applies in every position a folder can show up
     * in (tree, relations, properties, ...) and is independent of the workspaces.
     *
     * @param ElementInterface|OwnerAwareFieldInterface|null $element
     */
    public static function isFolderReadAllowed($element): bool
    {
        if ($element instanceof DataObject\Folder) {
            $setting = 'object_folder';
        } elseif ($element instanceof Document\Folder) {
            $setting = 'document_folder';
        } else {
            return true;
        }

        $context = RuntimeCache::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];

        return (bool) ($configuration->getSpecialEntities()[$setting]['read'] ?? false);
    }

    /**
     * @param ElementInterface|OwnerAwareFieldInterface|null $element
     * @param string $type
     *
     * @return bool
     *
     * @throws NotAllowedException
     */
    public static function checkPermission($element, $type)
    {
        $context = RuntimeCache::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];

        // a disabled folder read setting is not a workspace permission: it is not lifted by skipPermissionCheck
        // and not turned into an exception by the not-allowed policy, the folder simply resolves to null
        if ($type === 'read' && !self::isFolderReadAllowed($element)) {
            return false;
        }

        if ($configuration->skipPermisssionCheck()) {
            return true;
        }

        $event = new PermissionEvent($element, $type);
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = \Pimcore::getContainer()->get('event_dispatcher');
        $eventDispatcher->dispatch($event, PermissionEvents::PRE_CHECK);
        if (!$event->isGranted()) {
            if (PimcoreDataHubBundle::getNotAllowedPolicy() === PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION) {
                $path = $element?->getFullPath() ?? 'unknown element';

                throw new ClientSafeException('access for ' . $path . ' denied');
            }

            return false;
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
        $cacheKey = 'datahub_workspace_permissions_' . $configuration->getName() . '_' . $elementType;
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
        Cache::save($lookupTable, $cacheKey, self::getCacheTags($configuration));

        return $lookupTable;
    }

    private static function getCacheTags(Configuration $configuration): array
    {
        return ['datahub_workspace_permissions_'. $configuration->getName()];
    }
}
