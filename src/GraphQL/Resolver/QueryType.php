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
use  Pimcore\Bundle\DataHubBundle\GraphQL\Traits\PermissionInfoTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Listing;


class QueryType
{

    use ServiceTrait;
    use PermissionInfoTrait;

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
     * @param $omitPermissionCheck
     */
    public function __construct($class = null, $configuration = null, $omitPermissionCheck = false)
    {
        $this->class = $class;
        $this->configuration = $configuration;
        $this->omitPermissionCheck = $omitPermissionCheck;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveFolderGetter($value = null, $args = [], $context, ResolveInfo $resolveInfo = null, $elementType)
    {
        if ($args && $args['defaultLanguage']) {
            $this->getGraphQlService()->getLocaleService()->setLocale($args['defaultLanguage']);
        }

        if ($elementType == "asset") {
            $element = Asset\Folder::getById($args['id']);
        } else if ($elementType == "object") {
            $element = Folder::getById($args['id']);
        }

        if (!$element) {
            return null;
        }

        if (!WorkspaceHelper::isAllowed($element, $context['configuration'], 'read') && !$this->omitPermissionCheck) {
            if (PimcoreDataHubBundle::getNotAllowedPolicy() == PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION) {
                throw new \Exception('not allowed to view asset ' . $element->getFullPath());
            } else {
                return null;
            }
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
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveAssetFolderGetter($value = null, $args = [], $context, ResolveInfo $resolveInfo = null) {
        return $this->resolveFolderGetter($value, $args, $context, $resolveInfo, "asset");
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveObjectFolderGetter($value = null, $args = [], $context, ResolveInfo $resolveInfo = null) {
        return $this->resolveFolderGetter($value, $args, $context, $resolveInfo, "object");
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

        if (!WorkspaceHelper::isAllowed($assetElement, $context['configuration'], 'read') && !$this->omitPermissionCheck ) {
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
    public function resolveObjectGetter($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {

        if (!$args["id"]) {
            return null;
        }

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

        $objectList->setObjectTypes([AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_FOLDER, AbstractObject::OBJECT_TYPE_VARIANT]);
        $objectList->setLimit(1);
        $objectList->setUnpublished(1);
        $objectList = $objectList->load();
        if (!$objectList) {
            throw new \Exception('object with ID ' . $args["id"] . ' not found');
        }
        $object = $objectList[0];

        if (!WorkspaceHelper::isAllowed($object, $configuration, 'read') && !$this->omitPermissionCheck) {
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
        if (WorkspaceHelper::isAllowed($object, $this->configuration, 'read') && !$this->omitPermissionCheck) {
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
     * @return array
     * @throws \Exception
     */
    public function resolveListing($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        if ($args && $args['defaultLanguage']) {
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
        if (isset($args['sortBy'])) {
            $order = $args['sortOrder'] ? $args['sortOrder'] : 'ASC';
            $objectList->setOrderKey($args['sortBy']);
            $objectList->setOrder($order);
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
        $conditionParts[] = ' (
                                                    (select `read` from plugin_datahub_workspaces_object where configuration = ' . $db->quote($configuration->getName()) . ' and LOCATE(CONCAT(o_path,o_key),cpath)=1  ORDER BY LENGTH(cpath) DESC LIMIT 1)=1
                                                    OR
                                                    (select `read` from plugin_datahub_workspaces_object where configuration = ' . $db->quote($configuration->getName()) . ' and LOCATE(cpath,CONCAT(o_path,o_key))=1  ORDER BY LENGTH(cpath) DESC LIMIT 1)=1
                                                 )';

        if (isset($args['filter'])) {
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

        $objectList->setObjectTypes([AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_FOLDER, AbstractObject::OBJECT_TYPE_VARIANT]);

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

