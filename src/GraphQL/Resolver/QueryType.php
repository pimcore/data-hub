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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Language\AST\FragmentSpreadNode;
use GraphQL\Language\AST\InlineFragmentNode;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\AST\NodeList;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Helper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\PermissionInfoTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\ListingEvents;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\ListingEvent;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinition;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Listing;
use Pimcore\Model\Document;
use Pimcore\Model\Element\Service;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class QueryType
{

    use ServiceTrait;
    use PermissionInfoTrait;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var null
     */
    protected $class;

    /**
     * @var null
     */
    protected $configuration;

    /**
     * QueryType constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param ClassDefinition $class
     * @param $configuration
     * @param bool $omitPermissionCheck
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, $class = null, $configuration = null, $omitPermissionCheck = false)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->class = $class;
        $this->configuration = $configuration;
        $this->omitPermissionCheck = $omitPermissionCheck;
    }

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws ClientSafeException
     */
    public function resolveFolderGetter($value = null, $args = [], $context, ResolveInfo $resolveInfo = null, $elementType)
    {
        if ($args && isset($args['defaultLanguage'])) {
            $this->getGraphQlService()->getLocaleService()->setLocale($args['defaultLanguage']);
        }

        $element = null;

        if ($elementType == "asset") {
            $element = Asset\Folder::getById($args['id']);
        } else if ($elementType == "document") {
            $element = Document\Folder::getById($args['id']);
        } else if ($elementType == "object") {
            $element = Folder::getById($args['id']);
        }

        if (!$element) {
            return null;
        }

        if (!$this->omitPermissionCheck && !WorkspaceHelper::checkPermission($element, 'read')) {
            return null;
        }

        $data = new ElementDescriptor();
        $getter = "get" . ucfirst($elementType) . "FieldHelper";
        $fieldHelper = $this->getGraphQlService()->$getter();
        $fieldHelper->extractData($data, $element, $args, $context, $resolveInfo);
        $data = $data->getArrayCopy();
        return $data;
    }

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws ClientSafeException
     */
    public function resolveAssetFolderGetter($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        return $this->resolveFolderGetter($value, $args, $context, $resolveInfo, "asset");
    }


    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws ClientSafeException
     */
    public function resolveDocumentFolderGetter($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        return $this->resolveFolderGetter($value, $args, $context, $resolveInfo, "document");
    }

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws ClientSafeException
     */
    public function resolveObjectFolderGetter($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        return $this->resolveFolderGetter($value, $args, $context, $resolveInfo, "object");
    }

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws ClientSafeException
     */
    public function resolveDocumentGetter($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        if ($args && isset($args['defaultLanguage'])) {
            $this->getGraphQlService()->getLocaleService()->setLocale($args['defaultLanguage']);
        }

        $documentElement = null;

        if (isset($args['id'])) {
            $documentElement = Document::getById($args['id']);
        } else if (isset($args['path'])) {
            $documentElement = Document::getByPath($args['path']);
        }

        if (!$documentElement) {
            return null;
        }

        if (!$this->omitPermissionCheck) {
            if (!WorkspaceHelper::checkPermission($documentElement, 'read')) {
                return null;
            }
        }

        $data = new ElementDescriptor($documentElement);
        $this->getGraphQlService()->extractData($data, $documentElement, $args, $context, $resolveInfo);
        $data = $data->getArrayCopy();

        return $data;
    }


    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws ClientSafeException
     */
    public function resolveAssetGetter($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        if ($args && isset($args['defaultLanguage'])) {
            $this->getGraphQlService()->getLocaleService()->setLocale($args['defaultLanguage']);
        }

        $assetElement = Asset::getById($args['id']);
        if (!$assetElement) {
            return null;
        }

        if (!$this->omitPermissionCheck) {
            if (!WorkspaceHelper::checkPermission($assetElement, 'read')) {
                return null;
            }
        }

        $data = new ElementDescriptor($assetElement);
        $this->getGraphQlService()->extractData($data, $assetElement, $args, $context, $resolveInfo);
        return $data;
    }


    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws ClientSafeException
     */
    public function resolveObjectGetter($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {

        if (!$args["id"]) {
            return null;
        }

        if ($args && isset($args['defaultLanguage'])) {
            $this->getGraphQlService()->getLocaleService()->setLocale($args['defaultLanguage']);
        }

        $modelFactory = $this->getGraphQlService()->getModelFactory();
        $listClass = 'Pimcore\\Model\\DataObject\\' . ucfirst($this->class->getName()) . '\\Listing';
        /** @var Listing $objectList */
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

        $objectList->setObjectTypes([AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_FOLDER, AbstractObject::OBJECT_TYPE_VARIANT]);
        $objectList->setLimit(1);
        $objectList->setUnpublished(1);
        $objectList = $objectList->load();
        if (!$objectList) {
            throw new ClientSafeException('object with ID ' . $args["id"] . ' not found');
        }
        $object = $objectList[0];

        if (!$this->omitPermissionCheck) {
            if (!WorkspaceHelper::checkPermission($object, 'read')) {
                throw new ClientSafeException('permission denied. check your workspace settings');
            }
        }

        $data = new ElementDescriptor($object);
        $data['id'] = $object->getId();
        $this->getGraphQlService()->extractData($data, $object, $args, $context, $resolveInfo);

        return $data;
    }

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     * @return mixed
     */
    public function resolveEdge($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $nodeData = $value['node'];
        $objectId = $nodeData['id'];

        $object = AbstractObject::getById($objectId);

        $data = new ElementDescriptor();
        if ($this->omitPermissionCheck || WorkspaceHelper::checkPermission($object, 'read')) {
            $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();
            $nodeData = $fieldHelper->extractData($data, $object, $args, $context, $resolveInfo);
        }

        return $nodeData;
    }


    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     * @return mixed
     */
    public function resolveEdges($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        return $value['edges'];
    }

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveListing($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        if ($args && isset($args['defaultLanguage'])) {
            $this->getGraphQlService()->getLocaleService()->setLocale($args['defaultLanguage']);
        }

        $modelFactory = $this->getGraphQlService()->getModelFactory();
        $listClass = 'Pimcore\\Model\\DataObject\\' . ucfirst($this->class->getName()) . '\\Listing';
        /** @var Listing $objectList */
        $objectList = $modelFactory->build($listClass);
        $conditionParts = [];
        if (isset($args['ids'])) {
            $conditionParts[] = '(o_id IN (' . $args['ids'] . '))';
        }

        // paging
        if (isset($args['first'])) {
            $objectList->setLimit($args['first']);
        }

        if (isset($args['after'])) {
            $objectList->setOffset($args['after']);
        }

        // sorting
        if (!empty($args['sortBy'])) {
            $objectList->setOrderKey($args['sortBy']);
            if (!empty($args['sortOrder'])) {
                $objectList->setOrder($args['sortOrder']);
            }
        }

        // Include unpublished
        if (isset($args['published']) && $args['published'] === false) {
            $objectList->setUnpublished(true);
        }

        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $sqlListCondition = $configuration->getSqlObjectCondition();

        if ($sqlListCondition) {
            $conditionParts[] = '(' . $sqlListCondition . ')';
        }

        // check permissions
        $db = Db::get();
        $workspacesTableName = 'plugin_datahub_workspaces_object';
        $conditionParts[] = ' (
            (
                SELECT `read` from ' . $db->quoteIdentifier($workspacesTableName) . '
                WHERE ' . $db->quoteIdentifier($workspacesTableName) . '.configuration = ' . $db->quote($configuration->getName()) . '
                AND LOCATE(CONCAT(' . $db->quoteIdentifier($objectList->getTableName()) . '.o_path,' . $db->quoteIdentifier($objectList->getTableName()) . '.o_key),' . $db->quoteIdentifier($workspacesTableName) . '.cpath)=1
                ORDER BY LENGTH(' . $db->quoteIdentifier($workspacesTableName) . '.cpath) DESC
                LIMIT 1
            )=1
            OR
            (
                SELECT `read` from ' . $db->quoteIdentifier($workspacesTableName) . '
                WHERE ' . $db->quoteIdentifier($workspacesTableName) . '.configuration = ' . $db->quote($configuration->getName()) . '
                AND LOCATE(' . $db->quoteIdentifier($workspacesTableName) . '.cpath,CONCAT(' . $db->quoteIdentifier($objectList->getTableName()) . '.o_path,' . $db->quoteIdentifier($objectList->getTableName()) . '.o_key))=1
                ORDER BY LENGTH(' . $db->quoteIdentifier($workspacesTableName) . '.cpath) DESC
                LIMIT 1
            )=1
        )';

        if (isset($args['filter'])) {
            $filter = json_decode($args['filter'], false);
            if (!$filter) {
                throw new ClientSafeException('unable to decode filter');
            }
            $filterCondition = Helper::buildSqlCondition($objectList->getTableName(), $filter);
            $conditionParts[] = $filterCondition;
        }

        if ($conditionParts) {
            $condition = implode(' AND ', $conditionParts);
            $objectList->setCondition($condition);
        }

        $objectList->setObjectTypes([AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_FOLDER, AbstractObject::OBJECT_TYPE_VARIANT]);

        $event = new ListingEvent(
            $objectList,
            $args,
            $context,
            $resolveInfo
        );
        $this->eventDispatcher->dispatch(ListingEvents::PRE_LOAD, $event);
        $objectList = $event->getListing();

        $totalCount = $objectList->getTotalCount();
        $objectList = $objectList->load();

        $nodes = [];

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
    }


    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     * @return mixed
     */
    public function resolveListingTotalCount($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        return $value['totalCount'];
    }

    /**
     * Build a filter query.
     *
     * @TODO Create response format to provide facets.
     *
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveFilter($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        if ($args && $args['defaultLanguage']) {
            $this->getGraphQlService()->getLocaleService()->setLocale($args['defaultLanguage']);
        }
        $factory = Factory::getInstance();

        // Set tenant config.
        if (!empty($args['tenant'])) {
            $factory->getEnvironment()->setCurrentAssortmentTenant($args['tenant']);
        }

        /** @var \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface $resultList */
        $resultList = $factory->getIndexService()->getProductListForCurrentTenant();

        /** @var \Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinition $filterDefinition */
        $currentFilters = [];
        $facets = [];
        $filterDefinition = false;
        // Set default settings using a FilterDefinition if id is provided.
        if (!empty($args['filterDefinition'])) {
            if (isset($args['filterDefinition']['id'])) {
                $object = AbstractObject::getById($args['filterDefinition']['id']);
                if ($object instanceof AbstractFilterDefinition) {
                    $filterDefinition = $object;
                } else if ($object && isset($args['filterDefinition']['relationField'])) {
                    $getter = 'get' . ucfirst($args['filterDefinition']['relationField']);
                    if (method_exists($object, $getter)) {
                        $filterDefinition = $object->$getter();
                    }
                }
            }
            if (!($filterDefinition && $filterDefinition instanceof AbstractFilterDefinition)
                && isset($args['filterDefinition']['fallbackFilterDefinitionId'])
            ) {
                $filterDefinition = AbstractFilterDefinition::getById($args['filterDefinition']['fallbackFilterDefinitionId']);
            }
            if ($filterDefinition) {
                $filterService = $factory->getFilterService();

                if ($pageLimit = $filterDefinition->getPageLimit()) {
                    $resultList->setLimit($pageLimit);
                }

                $orderByField = null;
                $orderByDirection = null;

                $orderByList = [];
                if ($orderByCollection = $filterDefinition->getDefaultOrderBy()) {
                    foreach ($orderByCollection as $orderBy) {
                        if ($orderBy->getField()) {
                            $orderByList[] = [$orderBy->getField(), $orderBy->getDirection()];
                        }
                    }
                }
                $resultList->setOrderKey($orderByList);
                $resultList->setOrder('ASC');
                $filterValues = [];
                if (!empty($args['facets'])) {
                    foreach ($args['facets'] as $facet) {
                        $filterValues[$facet['field']] = $facet['values'];
                    }
                }
                // Read out requested filter from GraphQL Request Query to check if an output is necessary or not
                $filterNodes = null;
                /** @var NodeList $requestedFilters */
                $requestedFilters = $resolveInfo->operation->selectionSet->selections[0]->selectionSet->selections[0]->selectionSet->selections;

                foreach ($requestedFilters as $filter) {
                    if ($filter->name->value == 'facets') {
                        $filterNodes[] = $filter->selectionSet->selections;
                    }
                }

                //Facets could be multiple in a Request e.g. to separate filters and categories
                //Merge everything together
                if (count($filterNodes) >= 1) {
                    $tempFilterNodes = [];
                    foreach ($filterNodes as $filterNode) {
                        foreach ($filterNode as $node) {
                            $tempFilterNodes[] = $node;
                        }
                    }
                    $filterNodes = $tempFilterNodes;
                }

                $requestFilters = [];
                if (!empty($filterNodes)) {
                    foreach ($filterNodes as $filterNode) {
                        if ($filterNode->kind == NodeKind::FRAGMENT_SPREAD && $filters = $filterDefinition->getFilters()) {
                            /** @var FragmentSpreadNode $filterNode */
                            //check for fragments type name because fragments can have any name
                            foreach ($filters as $savedFilter) {
                                foreach ($resolveInfo->fragments as $fragment) {
                                    if (strpos($fragment->typeCondition->name->value, $savedFilter->getType()) !== false) {
                                        if ($filterNode->name->value == $fragment->name->value) {
                                            $requestFilters[] = $fragment->typeCondition->name->value;
                                        }
                                    }
                                }
                            }
                        }
                        if ($filterNode->kind == NodeKind::INLINE_FRAGMENT) {
                            /** @var InlineFragmentNode $filterNode */
                            $requestFilters[] = $filterNode->typeCondition->name->value;
                        }
                    }
                }

                if ($filters = $filterDefinition->getFilters()) {
                    foreach ($filters as $k => $filter) {
                        // Check if this filter can handle multiple values and if
                        // not use the first values entry.
                        $filterType = $filterService->getFilterType($filter->getType());
                        $field = \Pimcore\Bundle\DataHubBundle\FilterService\FilterType\HijackAbstractFilterType::getFieldFromFilter($filterType, $filter);

                        // Check if filter is requested from GraphQL Query
                        $hasFilter = false;
                        foreach ($requestFilters as $requestFilter) {
                            if (strpos($requestFilter, $filter->getType()) !== FALSE) {
                                $hasFilter = true;
                                break;
                            }
                        }
                        // If still adding field to facets which is not request an empty array is in the output result
                        if (!$hasFilter) {
                            continue;
                        }
                        if (!\Pimcore\Bundle\DataHubBundle\FilterService\FilterType\HijackAbstractFilterType::isMultiValueFilter($filterType, $filter)) {
                            if (isset($filterValues[$field])) {
                                $filterValues[$field] = current($filterValues[$field]);
                            }
                        }

                        $facets[$k] = [
                            'filter' => $filter,
                            'filterService' => $filterService,
                            'resultList' => $resultList,
                        ];
                    }
                }

                $currentFilters = $filterService->initFilterService($filterDefinition, $resultList, $filterValues);
            }
        }
        // paging
        if (isset($args['first'])) {
            $resultList->setLimit($args['first']);
        }
        if (isset($args['after'])) {
            $resultList->setOffset($args['after']);
        }

        // sorting
        if (!empty($args['sortBy'])) {
            if (!empty($args['sortOrder'])) {
                $resultList->setOrderKey(array_map(function ($a, $b) {
                    return [$a, $b];
                }, $args['sortBy'], $args['sortOrder']));
            } else {
                $resultList->setOrderKey($args['sortBy']);
            }
        }

        if (!empty($args['variantMode'])) {
            $resultList->setVariantMode($args['variantMode']);
        }

        if (!empty($args['fulltext'])) {
            if ($resultList instanceof \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\DefaultMysql) {
                $resultList->buildFulltextSearchWhere(
                    $resultList->getCurrentTenantConfig()->getSearchAttributes(),
                    $args['fulltext']
                );
                return $resultList->addCondition($args['fulltext'], 'relevance');
            } elseif ($resultList instanceof \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ElasticSearch\AbstractElasticSearch) {
                /** @var \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ElasticSearch\AbstractElasticSearch $resultList */
                $resultList->addQueryCondition($args['fulltext']);
            }
        }

        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        // @TODO Implement SQL Conditions in a generic way - we need to support
        // ElasticSearch.
        //@TODO Implement workspace limitation in a generic way.

        $db = Db::get();
        if ($resultList instanceof \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\DefaultMysql) {
            // Add SQL-Conditions.
            if ($sqlListCondition = $configuration->getSqlObjectCondition()) {
                $conditionParts[] = '(' . $sqlListCondition . ')';
            }
            // check permissions
            $conditionParts[] = ' (
                                    (select `read` from plugin_datahub_workspaces_object where configuration = ' . $db->quote($configuration->getName()) . ' and LOCATE(CONCAT(o_path,o_key),cpath)=1  ORDER BY LENGTH(cpath) DESC LIMIT 1)
                                    UNION
                                    (select `read` from plugin_datahub_workspaces_object where configuration = ' . $db->quote($configuration->getName()) . ' and LOCATE(cpath,CONCAT(o_path,o_key))=1  ORDER BY LENGTH(cpath) DESC LIMIT 1)
                                 )';
            if ($conditionParts) {
                $condition = implode(' AND ', $conditionParts);
                $resultList->addCondition($condition);
            }
        } elseif ($resultList instanceof \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ElasticSearch\AbstractElasticSearch) {
            /** @var \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ElasticSearch\AbstractElasticSearch $resultList */

            // @FIXME How can we convert that to DSL?
            // We might can use something like this:
            // https://www.elastic.co/guide/en/elasticsearch/reference/6.8/sql-spec.html
            // https://github.com/elastic/elasticsearch/tree/master/x-pack/plugin/sql
            // https://github.com/opendistro-for-elasticsearch/sql
            // $sqlListCondition = $configuration->getSqlObjectCondition();

            // Fetch readablePaths to implement a access filter.
            $readablePaths = $db->fetchCol('select `cpath` from plugin_datahub_workspaces_object where configuration = ? AND `read`=1 ORDER BY LENGTH(cpath)', [$configuration->getName()]);
            // @FIXME path is not part of the system parameters indexed - see
            // \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Worker\ElasticSearch\AbstractElasticSearch::getSystemAttributes()
            // We could hook into the indexing and add it automagically but that
            // seems intrusive.
            // $resultList->addCondition(['terms' => ['system.path' => $readablePaths]]);
        }

        /** @var \Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractCategory $category */
        if (!empty($args['category']) && ($category = AbstractObject::getById($args['category']))) {
            $resultList->setCategory($category);
        }
        // Price filter.
        if (isset($args['priceFrom']) && !isset($args['priceTo'])) {
            $resultList->addPriceCondition($args['priceFrom']);
        } elseif (isset($args['priceFrom']) && !isset($args['priceTo'])) {
            $resultList->addPriceCondition(null, $args['priceTo']);
        } elseif (isset($args['priceFrom']) && isset($args['priceTo'])) {
            $resultList->addPriceCondition($args['priceFrom'], $args['priceTo']);
        }
        $resultList->getInProductList(!isset($args['published']) || !empty($args['published']));

        $totalCount = $resultList->count();
        $objectList = $resultList->load();

        // Process result objects.
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
        $connection['facets'] = $facets;
        $connection['totalCount'] = $totalCount;

        return $connection;
    }


    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return mixed
     */
    public function resolveFilterTotalCount($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        return $value['totalCount'];
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return mixed
     */
    public function resolveFacets($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        //check which values are necessary if multiple facet arguments sent in the request
        //this prevents empty arrays in multiple facet types
        $facetName = end($resolveInfo->path);

        $filterNodes = null;
        /** @var NodeList $requestedFilters */
        $requestedFilters = $resolveInfo->operation->selectionSet->selections[0]->selectionSet->selections[0]->selectionSet->selections;

        foreach ($requestedFilters as $filter) {
            if ($filter->alias->value == $facetName) {
                $filterNodes = $filter->selectionSet->selections;
            }
        }
        $filterNames = [];
        $storeFragments = false;
        foreach ($filterNodes as $filterNode) {
            if ($filterNode->kind == NodeKind::FRAGMENT_SPREAD) {
                $storeFragments = true;
            }
            if ($filterNode->kind == NodeKind::INLINE_FRAGMENT) {
                $filterNames[] = $filterNode->typeCondition->name->value;
            }
        }
        //just store fragments one time
        if ($storeFragments) {
            //store all fragment type names because we don't have the FilterDefinition here
            foreach ($resolveInfo->fragments as $fragment) {
                $filterNames[] = $fragment->typeCondition->name->value;
            }
        }

        $facets = [];
        foreach ($value['facets'] as $facet) {
            $filter = $facet['filter'];
            $filterType = $filter->getType();
            foreach ($filterNames as $filterName) {
                if (strpos($filterName, $filterType) !== false) {
                    $facets[] = $facet;
                }
            }

        }
        if (!empty($facets)) {
            return $facets;
        }
        return $value['facets'];
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return mixed
     */
    public function resolveFacet($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $translator = $this->getGraphQlService()->getTranslator();

        $filter = $value['filter'];
        $filterService = $value['filterService'];
        $resultList = $value['resultList'];

        // Extract the facet information.
        /* @var \Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType $filter */
        $filterType = $filterService->getFilterType($filter->getType());
        $field = \Pimcore\Bundle\DataHubBundle\FilterService\FilterType\HijackAbstractFilterType::getFieldFromFilter($filterType, $filter);
        $options = $resultList->getGroupByValues($field, true, !method_exists($filter, 'getUseAndCondition') || !$filter->getUseAndCondition());

        foreach ($options as &$option) {
            $prefix = (is_numeric($option['value'])) ? $field . ':' : '';
            if (!empty($option['value'])) {
                $option['label'] = $translator->trans($prefix . $option['value']);
            } else {
                $option['label'] = $translator->trans('No Value');
            }
        }

        $value = [
            'filterType' => $filter->getType(),
            'field' => $field,
            'label' => $translator->trans($filter->getLabel()),
            'options' => $options,
        ];

        return isset($value[$resolveInfo->fieldName]) ? $value[$resolveInfo->fieldName] : null;
    }
}
