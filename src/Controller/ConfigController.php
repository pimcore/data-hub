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

namespace Pimcore\Bundle\DataHubBundle\Controller;

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Configuration\Dao;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
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
     * @param $path
     *
     * @param $name
     *
     * @return array
     */
    private function buildFolder($path, $name): array
    {
        return [
            'id' => $path,
            'text' => $name,
            'type' => 'folder',
            'expanded' => true,
            'iconCls' => 'pimcore_icon_folder',
            'children' => [],
        ];
    }

    /**
     * @param Configuration $configuration
     *
     * @return array
     */
    private function buildItem($configuration): array
    {
        $type = $configuration->getType() ?: 'graphql';

        return [
            'id' => $configuration->getName(),
            'text' => $configuration->getName(),
            'type' => 'config',
            'iconCls' => 'plugin_pimcore_datahub_icon_' . $type,
            'expandable' => false,
            'leaf' => true,
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

        $folders = Dao::getFolders();
        $list = Dao::getList();

        $tree = [];
        $folderStructure = [];

        // build a temporary 1 dimensional folder structure
        foreach ($folders as $folder) {
            $folderStructure[$folder['path']] = $this->buildFolder($folder['path'], $folder['name']);

            // root folders, keep a pointer to 1 dimensional array
            // to minimize memory and actually make the nesting work
            if (empty($folder['parent'])) {
                $tree[] = &$folderStructure[$folder['path']];
            }
        }

        // start nesting folders
        foreach ($folders as $folder) {
            $parent = $folder['parent'];
            $path = $folder['path'];

            if (!empty($parent) && !empty($folderStructure[$parent])) {
                $folderStructure[$parent]['children'][] = & $folderStructure[$path];
            }
        }

        // add configurations to their corresponding folder
        foreach ($list as $configuration) {
            $config = $this->buildItem($configuration);

            if (!$configuration->getPath()) {
                $tree[] = $config;
            } elseif (!empty($folderStructure[$configuration->getPath()])) {
                $folderStructure[$configuration->getPath()]['children'][] = $config;
            }
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

        try {
            $name = $request->get('name');

            $config = Dao::getByName($name);
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
     * @Route("/add-folder")
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function addFolderAction(Request $request): ?JsonResponse
    {
        $this->checkPermission(self::CONFIG_NAME);

        $parent = $request->get('parent');
        $name = $request->get('name');

        try {
            if (!$name) {
                throw new \Exception('Invalid name.');
            }

            Dao::addFolder($parent, $name);

            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @Route("/delete-folder")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteFolderAction(Request $request): ?JsonResponse
    {
        $this->checkPermission(self::CONFIG_NAME);

        $path = $request->get('path');

        if (Dao::getFolderByPath($path)) {
            Dao::deleteFolder($path);

            return $this->json(['success' => true]);
        } else {
            return $this->json(['success' => false]);
        }
    }

    /**
     * @Route("/move")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function moveAction(Request $request): JsonResponse
    {
        $this->checkPermission(self::CONFIG_NAME);

        $who = $request->get('who');
        $to = $request->get('to');

        Dao::moveConfiguration($who, $to);

        return new JsonResponse();
    }

    /**
     * @Route("/move-folder")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function moveFolderAction(Request $request): JsonResponse
    {
        $this->checkPermission(self::CONFIG_NAME);

        $who = $request->get('who');
        $to = $request->get('to');

        Dao::moveFolder($who, $to);

        return new JsonResponse();
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

        try {
            $path = $request->get('path');
            $name = $request->get('name');
            $type = $request->get('type');

            $config = Dao::getByName($name);

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

            $config = Dao::getByName($name);
            if ($config instanceof Configuration) {
                throw new \Exception('Name already exists.');
            }

            $originalName = $request->get('originalName');
            $originalConfig = Dao::getByName($originalName);
            if (!$originalConfig) {
                throw new \Exception('Configuration not found');
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
    public function getAction(Request $request, Service $graphQlService): JsonResponse
    {
        $this->checkPermission(self::CONFIG_NAME);

        $name = $request->get('name');

        $configuration = Dao::getByName($name);
        if (!$configuration) {
            throw new \Exception('Datahub configuration ' . $name . ' does not exist.');
        }

        $config = $configuration->getConfiguration();
        $config['schema']['queryEntities'] = array_values($config['schema']['queryEntities'] ?: []);
        $config['schema']['mutationEntities'] = array_values($config['schema']['mutationEntities'] ?: []);
        $config['schema']['specialEntities'] = $config['schema']['specialEntities'] ?: [];

        if (!$config['schema']['specialEntities']) {
            $config['schema']['specialEntities'] = [];
        }

        $specialSettings = ["document", "document_folder", "asset", "asset_folder", "object_folder"];
        foreach ($specialSettings as $key) {
            if (!$config['schema']['specialEntities'][$key]) {
                $config['schema']['specialEntities'][$key] = ["id" => $key];
            }
        }

        $config['schema']['specialEntities'] = array_values($config['schema']['specialEntities']);

        //TODO we probably need this stuff only for graphql stuff
        $supportedQueryDataTypes = $graphQlService->getSupportedDataObjectQueryDataTypes();
        $supportedMutationDataTypes = $graphQlService->getSupportedDataObjectMutationDataTypes();

        return new JsonResponse(
            [
                'name' => $configuration->getName(),
                'configuration' => $config,
                'supportedGraphQLQueryDataTypes' => $supportedQueryDataTypes,
                'supportedGraphQLMutationDataTypes' => $supportedMutationDataTypes,
                'modificationDate' => Dao::getConfigModificationDate()
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

            if ($modificationDate < Dao::getConfigModificationDate()) {
                throw new \Exception('The configuration was modified during editing, please reload the configuration and make your changes again');
            }

            $dataDecoded = json_decode($data, true);

            $name = $dataDecoded['general']['name'];
            $config = Dao::getByName($name);

            $keys = ['queryEntities', 'mutationEntities','specialEntities'];
            foreach ($keys as $key) {
                $transformedEntities = [];
                if ($dataDecoded['schema'][$key]) {
                    foreach ($dataDecoded['schema'][$key] as $entity) {
                        $transformedEntities[$entity['id']] = $entity;
                    }
                }
                $dataDecoded['schema'][$key] = $transformedEntities;
            }

            $config->setConfiguration($dataDecoded);
            $config->save();

            return $this->json(['success' => true, 'modificationDate' => Dao::getConfigModificationDate()]);
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

        $route = $routingService->getRouteCollection()->get('admin_pimcoredatahub_config');
        if ($route) {
            $url = $route->getPath();
            $url = str_replace('{clientname}', $name, $url);

            return $this->json(['explorerUrl' => $url]);
        } else {
            throw new \Exception('unable to resolve');
        }
    }
}
