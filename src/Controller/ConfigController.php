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

namespace Pimcore\Bundle\DataHubBundle\Controller;

use Pimcore\Bundle\DataHubBundle\ConfigEvents;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Event\AdminEvents;
use Pimcore\Bundle\DataHubBundle\Event\Config\SpecialEntitiesEvent;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\Model\SpecialEntitySetting;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Exception\ConfigWriteException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/admin/pimcoredatahub/config")
 */
class ConfigController extends \Pimcore\Bundle\AdminBundle\Controller\AdminController
{
    public const CONFIG_NAME = 'plugin_datahub_config';

    /**
     * @param Configuration $configuration
     *
     * @return array
     */
    private function buildItem($configuration): array
    {
        $type = $configuration->getType() ?: 'graphql';
        $name = $configuration->getName();

        return [
            'id' => $name,
            'text' => $name,
            'type' => 'config',
            'iconCls' => 'plugin_pimcore_datahub_icon_' . $type,
            'expandable' => false,
            'leaf' => true,
            'adapter' => $type,
            'writeable' => $configuration->isWriteable()
        ];
    }

    /**
     * @Route("/list")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listAction(Request $request): JsonResponse
    {
        // check permissions
        $this->checkPermission(self::CONFIG_NAME);

        $list = Configuration::getList();

        $event = new GenericEvent($this);
        $event->setArgument('list', $list);
        \Pimcore::getEventDispatcher()->dispatch($event, AdminEvents::CONFIGURATION_LIST);
        $list = $event->getArgument('list');

        $tree = [];

        $groups = [];
        /** @var Configuration $item */
        foreach ($list as $item) {
            if ($item->getGroup()) {
                if (empty($groups[$item->getGroup()])) {
                    $groups[$item->getGroup()] = [
                        'id' => 'group_' . $item->getName(),
                        'text' => $item->getGroup(),
                        'expandable' => true,
                        'leaf' => false,
                        'allowChildren' => true,
                        'iconCls' => 'pimcore_icon_folder',
                        'group' => $item->getGroup(),
                        'children' => [],
                    ];
                }
                $groups[$item->getGroup()]['children'][] = $this->buildItem($item);
            } else {
                $tree[] = $this->buildItem($item);
            }
        }

        foreach ($groups as $group) {
            $tree[] = $group;
        }

        return $this->json($tree);
    }

    /**
     * @Route("/delete")
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function deleteAction(Request $request): ?JsonResponse
    {
        $this->checkPermission(self::CONFIG_NAME);

        if ((new Configuration(null, null))->isWriteable() === false) {
            throw new ConfigWriteException();
        }

        try {
            $name = $request->get('name');

            $config = Configuration::getByName($name);
            if ($config->isWriteable() === false) {
                throw new ConfigWriteException();
            }
            if (!$config instanceof Configuration) {
                throw new \Exception('Name does not exist.');
            }

            WorkspaceHelper::deleteConfiguration($config);

            $config->delete();

            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @Route("/add")
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function addAction(Request $request): ?JsonResponse
    {
        $this->checkPermission(self::CONFIG_NAME);

        if ((new Configuration(null, null))->isWriteable() === false) {
            throw new ConfigWriteException();
        }

        try {
            $path = $request->get('path');
            $name = $request->get('name');
            $type = $request->get('type');

            $config = Configuration::getByName($name);

            if ($config instanceof Configuration) {
                throw new \Exception('Name already exists.');
            }

            $config = new Configuration($type, $path, $name);
            $config->save();

            return $this->json(['success' => true, 'name' => $name]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @Route("/clone")
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function cloneAction(Request $request): ?JsonResponse
    {
        $this->checkPermission(self::CONFIG_NAME);

        try {
            $name = $request->get('name');

            $config = Configuration::getByName($name);
            if ($config instanceof Configuration) {
                throw new \Exception('Name already exists.');
            }

            $originalName = $request->get('originalName');
            $originalConfig = Configuration::getByName($originalName);
            if (!$originalConfig) {
                throw new \Exception('Configuration not found');
            }
            if ($originalConfig->isWriteable() === false) {
                throw new ConfigWriteException();
            }

            $originalConfig->setName($name);
            $originalConfig->save();

            return $this->json(['success' => true, 'name' => $name]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @Route("/get")
     *
     * @param Request $request
     * @param Service $graphQlService
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function getAction(Request $request, Service $graphQlService, EventDispatcherInterface $eventDispatcher): JsonResponse
    {
        $this->checkPermission(self::CONFIG_NAME);

        $name = $request->get('name');

        $configuration = Configuration::getByName($name);
        if (!$configuration) {
            throw new \Exception('Datahub configuration ' . $name . ' does not exist.');
        }

        $config = $configuration->getConfiguration();
        $config['schema']['queryEntities'] = array_values($config['schema']['queryEntities'] ?? []);
        $config['schema']['mutationEntities'] = array_values($config['schema']['mutationEntities'] ?? []);
        $config['schema']['specialEntities'] = $config['schema']['specialEntities'] ?? [];

        if (!$config['schema']['specialEntities']) {
            $config['schema']['specialEntities'] = [];
        }

        $coreSettings = [
            new SpecialEntitySetting(
                'document',
                true,
                true,
                true,
                true,
                $config['schema']['specialEntities']['document']['read'] ?? false,
                $config['schema']['specialEntities']['document']['create'] ?? false,
                $config['schema']['specialEntities']['document']['update'] ?? false,
                $config['schema']['specialEntities']['document']['delete'] ?? false
            ),
            new SpecialEntitySetting(
                'document_folder',
                true,
                false,
                false,
                true,
                $config['schema']['specialEntities']['document_folder']['read'] ?? false,
                $config['schema']['specialEntities']['document_folder']['create'] ?? false,
                $config['schema']['specialEntities']['document_folder']['update'] ?? false,
                $config['schema']['specialEntities']['document_folder']['delete'] ?? false
            ),
            new SpecialEntitySetting(
                'asset',
                true,
                true,
                true,
                true,
                $config['schema']['specialEntities']['asset']['read'] ?? false,
                $config['schema']['specialEntities']['asset']['create'] ?? false,
                $config['schema']['specialEntities']['asset']['update'] ?? false,
                $config['schema']['specialEntities']['asset']['delete'] ?? false
            ),
            new SpecialEntitySetting(
                'asset_folder',
                true,
                true,
                true,
                true,
                $config['schema']['specialEntities']['asset_folder']['read'] ?? false,
                $config['schema']['specialEntities']['asset_folder']['create'] ?? false,
                $config['schema']['specialEntities']['asset_folder']['update'] ?? false,
                $config['schema']['specialEntities']['asset_folder']['delete'] ?? false
            ),
            new SpecialEntitySetting(
                'asset_listing',
                true,
                true,
                true,
                true,
                $config['schema']['specialEntities']['asset_listing']['read'] ?? false,
                $config['schema']['specialEntities']['asset_listing']['create'] ?? false,
                $config['schema']['specialEntities']['asset_listing']['update'] ?? false,
                $config['schema']['specialEntities']['asset_listing']['delete'] ?? false
            ),
            new SpecialEntitySetting(
                'object_folder',
                true,
                true,
                true,
                true,
                $config['schema']['specialEntities']['object_folder']['read'] ?? false,
                $config['schema']['specialEntities']['object_folder']['create'] ?? false,
                $config['schema']['specialEntities']['object_folder']['update'] ?? false,
                $config['schema']['specialEntities']['object_folder']['delete'] ?? false
            )
        ];

        $specialSettingsEvent = new SpecialEntitiesEvent($coreSettings, $config);
        $eventDispatcher->dispatch($specialSettingsEvent, ConfigEvents::SPECIAL_ENTITIES);

        $finalSettings = [];

        foreach ($specialSettingsEvent->getSpecialSettings() as $item) {
            $finalSettings[$item->getName()] = $item;
        }

        $config['schema']['specialEntities'] = $specialSettingsEvent->getSpecialSettings();

        //TODO we probably need this stuff only for graphql stuff
        $supportedQueryDataTypes = $graphQlService->getSupportedDataObjectQueryDataTypes();
        $supportedMutationDataTypes = $graphQlService->getSupportedDataObjectMutationDataTypes();

        return new JsonResponse(
            [
                'name' => $configuration->getName(),
                'configuration' => $config,
                'supportedGraphQLQueryDataTypes' => $supportedQueryDataTypes,
                'supportedGraphQLMutationDataTypes' => $supportedMutationDataTypes,
                'modificationDate' => $config['general']['modificationDate']
            ]
        );
    }

    /**
     * @Route("/save")
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function saveAction(Request $request): ?JsonResponse
    {
        $this->checkPermission(self::CONFIG_NAME);

        try {
            $data = $request->get('data');
            $modificationDate = $request->get('modificationDate', 0);

            $dataDecoded = json_decode($data, true);

            $name = $dataDecoded['general']['name'];
            $config = Configuration::getByName($name);
            if ($config->isWriteable() === false) {
                throw new ConfigWriteException();
            }
            $configuration = $config->getConfiguration();

            $savedModificationDate = 0;

            if ($configuration && isset($configuration['general']['modificationDate'])) {
                $savedModificationDate = $configuration['general']['modificationDate'];
            }

            if ($modificationDate < $savedModificationDate) {
                throw new \Exception('The configuration was modified during editing, please reload the configuration and make your changes again');
            }

            $dataDecoded['general']['modificationDate'] = time();

            $keys = ['queryEntities', 'mutationEntities'];
            foreach ($keys as $key) {
                $transformedEntities = [];
                if ($dataDecoded['schema'][$key]) {
                    foreach ($dataDecoded['schema'][$key] as $entity) {
                        $transformedEntities[$entity['id']] = $entity;
                    }
                }
                $dataDecoded['schema'][$key] = $transformedEntities;
            }

            if ($dataDecoded['schema']['specialEntities']) {
                $transformedEntities = [];

                foreach ($dataDecoded['schema']['specialEntities'] as $entity) {
                    $transformedEntities[$entity['name']] = [
                        'read' => $entity['readAllowed'],
                        'create' => $entity['createAllowed'],
                        'update' => $entity['updateAllowed'],
                        'delete' => $entity['deleteAllowed'],
                    ];

                    $dataDecoded['schema']['specialEntities'] = $transformedEntities;
                }
            }

            $config->setConfiguration($dataDecoded);
            $config->save();

            return $this->json(['success' => true, 'modificationDate' => $dataDecoded['general']['modificationDate']]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @Route("/get-explorer-url")
     *
     * @param RouterInterface $routingService
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function getExplorerUrlAction(RouterInterface $routingService, Request $request): ?JsonResponse
    {
        $name = $request->get('name');

        $url = $routingService->generate('admin_pimcoredatahub_config', ['clientname' => $name]);
        if ($url) {
            return $this->json(['explorerUrl' => $url]);
        } else {
            throw new \Exception('unable to resolve');
        }
    }

    /**
     * @Route("/thumbnail-tree")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function thumbnailTreeAction(Request $request)
    {
        $this->checkPermission('thumbnails');

        $thumbnails = [];

        $list = new \Pimcore\Model\Asset\Image\Thumbnail\Config\Listing();
        $items = $list->load();

        foreach ($items as $item) {
            $thumbnails[] = [
                'id' => $item->getName(),
                'text' => $item->getName()
            ];
        }

        return $this->adminJson($thumbnails);
    }
}
