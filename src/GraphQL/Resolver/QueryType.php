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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\ListingEvents;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\ListingEvent;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\GraphQL\Helper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ElementIdentificationTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\PermissionInfoTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Db;
use Pimcore\Logger;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Listing;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class QueryType
{
    use ServiceTrait;
    use PermissionInfoTrait;
    use ElementIdentificationTrait;

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
     *
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
     * @param string|null $elementType
     *
     * @return array
     *
     * @throws ClientSafeException
     */
    public function resolveFolderGetter($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null, $elementType = null)
    {
        if ($args && isset($args['defaultLanguage'])) {
            $this->getGraphQlService()->getLocaleService()->setLocale($args['defaultLanguage']);
        }

        $element = $this->getElementByTypeAndIdOrPath($args, $elementType);

        if (!$element) {
            return null;
        }

        if (!$this->omitPermissionCheck && !WorkspaceHelper::checkPermission($element, 'read')) {
            return null;
        }

        $data = new ElementDescriptor();
        $getter = 'get' . ucfirst($elementType) . 'FieldHelper';
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
     *
     * @return array
     *
     * @throws ClientSafeException
     */
    public function resolveAssetFolderGetter($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveFolderGetter($value, $args, $context, $resolveInfo, 'asset');
    }

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array
     *
     * @throws ClientSafeException
     */
    public function resolveDocumentFolderGetter($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveFolderGetter($value, $args, $context, $resolveInfo, 'document');
    }

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array
     *
     * @throws ClientSafeException
     */
    public function resolveObjectFolderGetter($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveFolderGetter($value, $args, $context, $resolveInfo, 'object');
    }

    /**
     * @deprecated args['path'] will no longer be supported by Release 1.0. Use args['fullpath'] instead.
     *
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array
     *
     * @throws ClientSafeException
     */
    public function resolveDocumentGetter($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        if ($args && isset($args['defaultLanguage'])) {
            $this->getGraphQlService()->getLocaleService()->setLocale($args['defaultLanguage']);
        }

        // TODO: remove this workaround for Release 1.0
        if ($args['path'] ?? false) {
            Logger::warn("Argument 'path' deprecated: will no longer be supported by Release 1.0. Use 'fullpath' instead.");
            $args['fullpath'] = $args['path'];
        }

        $documentElement = $this->getElementByTypeAndIdOrPath($args, 'document');

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
     *
     * @return array
     *
     * @throws ClientSafeException
     */
    public function resolveAssetGetter($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        if ($args && isset($args['defaultLanguage'])) {
            $this->getGraphQlService()->getLocaleService()->setLocale($args['defaultLanguage']);
        }

        $assetElement = $this->getElementByTypeAndIdOrPath($args, 'asset');
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
     *
     * @return array
     *
     * @throws ClientSafeException
     */
    public function resolveObjectGetter($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $isIdSet = $args['id'] ?? false;
        $isFullpathSet = $args['fullpath'] ?? false;

        if (!$isIdSet && !$isFullpathSet) {
            throw new ClientSafeException('object id or fullpath expected');
        }

        if ($args['defaultLanguage'] ?? false) {
            $this->getGraphQlService()->getLocaleService()->setLocale($args['defaultLanguage']);
        }

        $modelFactory = $this->getGraphQlService()->getModelFactory();
        $listClass = 'Pimcore\\Model\\DataObject\\' . ucfirst($this->class->getName()) . '\\Listing';
        /** @var Listing $objectList */
        $objectList = $modelFactory->build($listClass);
        $conditionParts = [];

        if ($isIdSet) {
            $conditionParts[] = '(o_id =' . $args['id'] . ')';
        }

        if ($isFullpathSet) {
            $fullpath = Service::correctPath($args['fullpath']);
            $conditionParts[] = '(concat(o_path, o_key) =' . Db::get()->quote($fullpath) . ')';
        }

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
            $errorMessage = $this->createArgumentErrorMessage($isFullpathSet, $isIdSet, $args);
            throw new ClientSafeException($errorMessage);
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
     *
     * @return mixed
     */
    public function resolveEdge($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $object = $value['node'];

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
     *
     * @return mixed
     */
    public function resolveEdges($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $objectList = $value['edges']();
        $nodes = [];

        foreach ($objectList as $object) {
            if (!$this->omitPermissionCheck && !WorkspaceHelper::checkPermission($object, 'read')) {
                continue;
            }

            $data = [];
            $data['id'] = $object->getId();
            $nodes[] = [
                'cursor' => 'object-' . $object->getId(),
                'node' => $object,
            ];
        }

        return $nodes;
    }

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array
     *
     * @throws \Exception
     */
    public function resolveListing($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        if ($args && isset($args['defaultLanguage'])) {
            $this->getGraphQlService()->getLocaleService()->setLocale($args['defaultLanguage']);
        }

        $db = Db::get();
        $modelFactory = $this->getGraphQlService()->getModelFactory();
        $listClass = 'Pimcore\\Model\\DataObject\\' . ucfirst($this->class->getName()) . '\\Listing';
        /** @var Listing $objectList */
        $objectList = $modelFactory->build($listClass);

        $conditionParts = [];
        $db = Db::get();
        if (isset($args['ids'])) {
            // Explode it and then quote it
            if (!is_array($args['ids'])) {
                $args['ids'] = explode(',', $args['ids']);
            }
            $ids = implode(', ', array_map([$db, 'quote'], $args['ids']));
            $conditionParts[] = '(o_id IN (' . $ids . '))';
        }
        if (isset($args['fullpaths'])) {
            $quotedFullpaths = array_map(
                static function ($fullpath) use ($db) {
                    $fullpath = trim($fullpath, " '");
                    $fullpath = Service::correctPath($fullpath);

                    return $db->quote($fullpath);
                },
                explode(',', $args['fullpaths'])
            );
            $conditionParts[] = '(concat(o_path, o_key) IN (' . implode(',', $quotedFullpaths) . '))';
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

            $className = $this->class->getName();
            $columns = $this->configuration->configuration['schema']['queryEntities'][$className]['columnConfig']['columns'];

            Helper::addJoins($objectList, $filter, $columns, $mappingTable);

            $filterCondition = Helper::buildSqlCondition($objectList->getTableName(), $filter, null, null, $mappingTable);
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
        $this->eventDispatcher->dispatch($event, ListingEvents::PRE_LOAD);
        $objectList = $event->getListing();

        $connection = [];
        $connection['edges'] = [$objectList, 'load'];
        $connection['totalCount'] = [$objectList, 'getTotalCount'];

        return $connection;
    }

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return mixed
     */
    public function resolveListingTotalCount($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $value['totalCount']();
    }

    private function createArgumentErrorMessage($isFullpathSet, $isIdSet, $args)
    {
        if ($isIdSet && $isFullpathSet) {
            return 'either id or fullpath expected but not both';
        }
        if ($isIdSet) {
            return "object with id:'" . $args['id'] . "' not found";
        }
        if ($isFullpathSet) {
            return "object with fullpath:'" . $args['fullpath'] . "' not found";
        }

        return 'either id or fullpath expected';
    }
}
