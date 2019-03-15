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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Query;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\AdminBundle\Controller\Rest\Helper;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\Type\AssetType;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Listing;

class QueryType extends ObjectType
{
    /**
     * QueryType constructor.
     *
     * @param array $config
     * @param array $context
     *
     * @throws \Exception
     */
    public function __construct($config = [], $context = [])
    {
        if (!$config['name']) {
            $config['name'] = 'Query';
        }

        $this->build($config, $context);
        parent::__construct($config);
    }

    public function buildAssetQueries(&$config = [], $context = [])
    {

        // GETTER DEFINITION
        $defGet = [
            'name' => 'getAsset',
            'args' => [
                'id' => ['type' => Type::nonNull(Type::int())],
                'defaultLanguage' => ['type' => Type::string()],
            ],
            'type' => AssetType::getInstance(),
            'resolve' => function ($value = null, $args = [], $context, ResolveInfo $resolveInfo = null) {
                if ($args && $args['defaultLanguage']) {
                    $localeService = \Pimcore::getContainer()->get('pimcore.locale');
                    $localeService->setLocale($args['defaultLanguage']);
                }

                $assetElement = Asset::getById($args['id']);
                if (!$assetElement) {
                    return null;
                }

                if (!WorkspaceHelper::isAllowed($assetElement, $context['configuration'], 'read')) {
                    if (PimcoreDataHubBundle::getNotAllowedPolicy() == PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION) {
                        throw new \Exception('not allowed to view asset ' . $assetElement->getFullPath());
                    } else {
                        return null;
                    }
                }

                $data = new \ArrayObject();
                $data->setFlags(\ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);

                $fieldHelper = \Pimcore::getContainer()->get('pimcore.datahub.graphql.fieldhelper.asset');
                $fieldHelper->extractData($data, $assetElement, $args, $context, $resolveInfo);
                $data = $data->getArrayCopy();

                if ($data['data']) {
                    $data['data'] = base64_encode($data['data']);
                }

                return $data;
            },
        ];

        $config['fields']['getAsset'] = $defGet;
    }

    public function buildDataObjectQueries(&$config = [], $context = [])
    {
        $listing = new ClassDefinition\Listing();
        $listing = $listing->load();

        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getQueryEntities();

        foreach ($listing as $class) {
            $className = $class->getName();
            if (!in_array($className, $entities)) {
                continue;
            }

            // GETTER DEFINITION
            $defGet = [
                'name' => 'get' . ucfirst($class->getName()),
                'args' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                    'defaultLanguage' => ['type' => Type::string()],
                ],
                'type' => \Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions::get($class),
                'resolve' => function ($value = null, $args = [], $context, ResolveInfo $resolveInfo = null) use ($class
                ) {
                    if ($args && $args['defaultLanguage']) {
                        $localeService = \Pimcore::getContainer()->get('pimcore.locale');
                        $localeService->setLocale($args['defaultLanguage']);
                    }

                    $modelFactory = \Pimcore::getContainer()->get('pimcore.model.factory');
                    $listClass = 'Pimcore\\Model\\DataObject\\' . ucfirst($class->getName()) . '\\Listing';
                    /** @var $listClass Listing */
                    $objectList = $modelFactory->build($listClass);
                    $conditionParts = [];

                    $conditionParts[] = '(o_id =' . $args['id'] . ')';

                    /** @var $configuration Configuration */
                    $configuration = $context['configuration'];
                    $sqlGetCondition = $configuration->getSqlObjectCondition();

                    if ($sqlGetCondition) {
                        $conditionParts[] = '(' . $sqlGetCondition . ')';
                    }

                    if ($conditionParts) {
                        $condition = implode(' AND ', $conditionParts);
                        $objectList->setCondition($condition);
                    }

                    $objectList->setLimit(1);
                    $objectList = $objectList->load();
                    if (!$objectList) {
                        throw new \Exception('element not found');
                    }
                    $object = $objectList[0];

                    if (!WorkspaceHelper::isAllowed($object, $configuration, 'read')) {
                        throw new \Exception('permission denied. check your workspace settings');
                    }

                    $data = [];
                    $data['id'] = $object->getId();
                    $fieldHelper = \Pimcore::getContainer()->get('pimcore.datahub.graphql.fieldhelper.object');
                    $fieldHelper->extractData($data, $object, $args, $context, $resolveInfo);

                    return $data;
                },
            ];

            $edgeType = new ObjectType(
                [
                    'name' => ucfirst($class->getName()) . 'Edge',
                    'fields' => [
                        'cursor' => Type::string(),
                        'node' => [
                            'type' => \Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions::get($class),
                            'resolve' => function (
                                $value = null,
                                $args = [],
                                $context,
                                ResolveInfo $resolveInfo = null
                            ) use ($class, $configuration
                            ) {
                                $nodeData = $value['node'];
                                $objectId = $nodeData['id'];

                                $object = AbstractObject::getById($objectId);

                                $data = [];
                                if (WorkspaceHelper::isAllowed($object, $configuration, 'read')) {
                                    $fieldHelper = \Pimcore::getContainer()->get('pimcore.datahub.graphql.fieldhelper.object');
                                    $nodeData = $fieldHelper->extractData($data, $object, $args, $context, $resolveInfo);
                                }

                                return $nodeData;
                            },
                        ],
                    ],
                ]
            );

            // LISTING DEFINITION
            $listingType = new ObjectType(
                [
                    'name' => ucfirst($class->getName()) . 'Connection',
                    'fields' => [

                        'edges' => [
                            'type' => Type::listOf($edgeType),
                            'resolve' => function (
                                $value = null,
                                $args = [],
                                $context,
                                ResolveInfo $resolveInfo = null
                            ) use ($class
                            ) {
                                return $value['edges'];
                            },
                        ],
                        'totalCount' => [
                            'description' => 'description for total count',
                            'resolve' => function ($value = null,
                                                   $args = [],
                                                   $context,
                                                   ResolveInfo $resolveInfo = null) {
                                return $value['totalCount'];
                            },
                            'type' => Type::int()
                        ]

                    ]
                ]
            );

            $defListing = [
                'name' => 'get' . ucfirst($class->getName()) . 'Listing',
                'args' => [
                    'ids' => ['type' => Type::string()],
                    'defaultLanguage' => ['type' => Type::string()],
                    'first' => ['type' => Type::int()],
                    'after' => ['type' => Type::int()],
                    'sortBy' => ['type' => Type::string()],
                    'sortOrder' => ['type' => Type::string()],
                    'filter' => ['type' => Type::string()]
                ],
                'type' => $listingType,
                'resolve' => function ($value = null, $args = [], $context, ResolveInfo $resolveInfo = null) use ($class
                ) {
                    if ($args && $args['defaultLanguage']) {
                        $localeService = \Pimcore::getContainer()->get('pimcore.locale');
                        $localeService->setLocale($args['defaultLanguage']);
                    }

                    $modelFactory = \Pimcore::getContainer()->get('pimcore.model.factory');
                    $listClass = 'Pimcore\\Model\\DataObject\\' . ucfirst($class->getName()) . '\\Listing';
                    /** @var $listClass Listing */
                    $objectList = $modelFactory->build($listClass);
                    $conditionParts = [];
                    if ($args['ids']) {
                        $conditionParts[] = '(o_id IN (' . $args['ids'] . '))';
                    }

                    // paging
                    if ($args['first']) {
                        $objectList->setLimit($args['first']);
                    }

                    if ($args['after']) {
                        $objectList->setOffset($args['after']);
                    }

                    // sorting
                    if ($args['sortBy']) {
                        $order = $args['sortOrder'] ? $args['sortOrder'] : 'ASC';
                        $objectList->setOrderKey($args['sortBy']);
                        $objectList->setOrder($order);
                    }

                    /** @var $configuration Configuration */
                    $configuration = $context['configuration'];
                    $sqlListCondition = $configuration->getSqlObjectCondition();

                    if ($sqlListCondition) {
                        $conditionParts[] = '(' . $sqlListCondition . ')';
                    }

                    // check permissions
                    $db = Db::get();
                    $conditionParts[] = ' (
                                                    (select `read` from plugin_datahub_workspaces_object where configuration = ' . $db->quote($configuration->getName()) . ' and LOCATE(CONCAT(o_path,o_key),cpath)=1  ORDER BY LENGTH(cpath) DESC LIMIT 1)=1
                                                    OR
                                                    (select `read` from plugin_datahub_workspaces_object where configuration = ' . $db->quote($configuration->getName()) . ' and LOCATE(cpath,CONCAT(o_path,o_key))=1  ORDER BY LENGTH(cpath) DESC LIMIT 1)=1
                                                 )';

                    if ($args['filter']) {
                        $filter = json_decode($args['filter'], false);
                        if (!$filter) {
                            throw new \Exception('unable to decode filter');
                        }
                        $filterCondition = Helper::buildSqlCondition($filter);
                        $conditionParts[] = $filterCondition;
                    }

                    if ($conditionParts) {
                        $condition = implode(' AND ', $conditionParts);
                        $objectList->setCondition($condition);
                    }

                    $totalCount = $objectList->getTotalCount();
                    $objectList = $objectList->load();

                    foreach ($objectList as $object) {
                        $data = [];
                        $data['id'] = $object->getId();
                        $nodes[] = [
                            'cursor' => 'object-' . $object->getId(),
                            'node' => $data,
                        ];
                    }
                    $connection = [];
                    $connection['edges'] = $nodes;
                    $connection['totalCount'] = $totalCount;

                    return $connection;
                },

            ];

            if (!$config['fields']) {
                $config['fields'] = [];
            }
            $config['fields']['get' . ucfirst($class->getName()) . 'Listing'] = $defListing;
            $config['fields']['get' . ucfirst($class->getName())] = $defGet;
        }
    }

    /**
     * @param array $config
     * @param array $context
     *
     * @throws \Exception
     */
    public function build(&$config = [], $context = [])
    {
        $this->buildAssetQueries($config, $context);
        $this->buildDataObjectQueries($config, $context);
    }
}
