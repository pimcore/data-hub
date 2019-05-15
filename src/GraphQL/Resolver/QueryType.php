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

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Listing;


class QueryType
{

    use ServiceTrait;

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
     * @param $class
     * @param $configuration
     */
    public function __construct($class = null, $configuration = null)
    {
        $this->class = $class;
        $this->configuration = $configuration;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveAssetGetter($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        if ($args && $args['defaultLanguage']) {
            $this->getGraphQlService()->getLocaleService()->setLocale($args['defaultLanguage']);
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

        $data = new ElementDescriptor();
        $fieldHelper = $this->getGraphQlService()->getAssetFieldHelper();
        $fieldHelper->extractData($data, $assetElement, $args, $context, $resolveInfo);
        $data = $data->getArrayCopy();

        if ($data['data']) {
            $data['data'] = base64_encode($data['data']);
        }

        return $data;

    }


    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveGetter($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {

        if ($args && $args['defaultLanguage']) {
            $this->getGraphQlService()->getLocaleService()->setLocale($args['defaultLanguage']);
        }

        $modelFactory = $this->getGraphQlService()->getModelFactory();
        $listClass = 'Pimcore\\Model\\DataObject\\' . ucfirst($this->class->getName()) . '\\Listing';
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
        $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();
        $fieldHelper->extractData($data, $object, $args, $context, $resolveInfo);

        return $data;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return mixed
     */
    public function resolveEdge($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $nodeData = $value['node'];
        $objectId = $nodeData['id'];

        $object = AbstractObject::getById($objectId);

        $data = [];
        if (WorkspaceHelper::isAllowed($object, $this->configuration, 'read')) {
            $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();
            $nodeData = $fieldHelper->extractData($data, $object, $args, $context, $resolveInfo);
        }

        return $nodeData;
    }


    /**
     * @param null $value
     * @param array $args
     * @param $context
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
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return mixed
     */
    public function resolveListing($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        if ($args && $args['defaultLanguage']) {
            $this->getGraphQlService()->getLocaleService()->setLocale($args['defaultLanguage']);
        }

        $modelFactory = $this->getGraphQlService()->getModelFactory();
        $listClass = 'Pimcore\\Model\\DataObject\\' . ucfirst($this->class->getName()) . '\\Listing';
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

        // Include unpublished
        if ($args['published'] === false) {
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
            $filterCondition = \Pimcore\Bundle\AdminBundle\Controller\Rest\Helper::buildSqlCondition($filter);
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
    }


    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return mixed
     */
    public function resolveListingTotalCount($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        return $value['totalCount'];
    }

}

