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
use Pimcore\Model\Translation;
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
     * @var ClassDefinition|null
     */
    protected $class;

    /**
     * @var object
     */
    protected $configuration;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param ClassDefinition|null $class
     * @param object $configuration
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
     * @param ElementDescriptor|null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     * @param string|null $elementType
     *
     * @return array|null
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
     * @param ElementDescriptor|null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array|null
     *
     * @throws ClientSafeException
     */
    public function resolveAssetFolderGetter($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveFolderGetter($value, $args, $context, $resolveInfo, 'asset');
    }

    /**
     * @param ElementDescriptor|null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array|null
     *
     * @throws ClientSafeException
     */
    public function resolveDocumentFolderGetter($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveFolderGetter($value, $args, $context, $resolveInfo, 'document');
    }

    /**
     * @param ElementDescriptor|null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array|null
     *
     * @throws ClientSafeException
     */
    public function resolveObjectFolderGetter($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveFolderGetter($value, $args, $context, $resolveInfo, 'object');
    }

    /**
     * @param ElementDescriptor|null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array|null
     *
     * @throws ClientSafeException
     *
     * @deprecated args['path'] will no longer be supported by Release 1.0. Use args['fullpath'] instead.
     *
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
     * @param ElementDescriptor|null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return ElementDescriptor|null
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
     * @throws \Exception
     */
    public function resolveTranslationGetter(mixed $value = null, array $args = [], array $context = [], ResolveInfo $resolveInfo = null): array
    {
        if (empty($args['key'])) {
            throw new \Exception('Argument key is mandatory');
        }

        $domain = 'messages';
        if (!empty($args['domain'])) {
            $domain = $args['domain'];
        }

        $languages = [];
        if (!empty($args['languages'])) {
            $languages = str_replace(' ', '', $args['languages']);
            $languages = explode(',', $languages);
        }

        $translation = Translation::getByKey($args['key'], $domain, false, false, $languages);
        if (!$translation) {
            return [];
        }

        $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();

        return $fieldHelper->extractData($data, $translation, $args, $context, $resolveInfo);
    }

    /**
     * @param ElementDescriptor|null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return ElementDescriptor
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
            $conditionParts[] = sprintf('(%s =' . $args['id'] . ')', Service::getVersionDependentDatabaseColumnName('o_id'));
        }

        if ($isFullpathSet) {
            $fullpath = Service::correctPath($args['fullpath']);
            $conditionParts[] = sprintf('(CONCAT(`%s`,`%s`) =' . Db::get()->quote($fullpath) . ')',
                Service::getVersionDependentDatabaseColumnName('o_path'),
                Service::getVersionDependentDatabaseColumnName('o_key'));
        }

        /** @var Configuration $configuration */
        $configuration = $context['configuration'];
        $sqlGetCondition = $configuration->getSqlObjectCondition();

        if ($sqlGetCondition) {
            $conditionParts[] = '(' . $sqlGetCondition . ')';
        }

        $condition = implode(' AND ', $conditionParts);
        $objectList->setCondition($condition);

        $objectList->setObjectTypes([AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_FOLDER, AbstractObject::OBJECT_TYPE_VARIANT]);
        $objectList->setLimit(1);
        $objectList->setUnpublished(true);
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
     * @param array|null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array
     */
    public function resolveEdge($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $object = $value['node'];
        $nodeData = [];

        if ($this->omitPermissionCheck || WorkspaceHelper::checkPermission($object, 'read')) {
            $data = new ElementDescriptor();
            $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();
            $nodeData = $fieldHelper->extractData($data, $object, $args, $context, $resolveInfo);
        }

        return $nodeData;
    }

    /**
     * @param array|null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array
     */
    public function resolveEdges($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $objectList = $value['edges']();
        $nodes = [];

        foreach ($objectList as $object) {
            if (!$this->omitPermissionCheck && !WorkspaceHelper::checkPermission($object, 'read')) {
                continue;
            }

            $nodes[] = [
                'cursor' => 'object-' . $object->getId(),
                'node' => $object,
            ];
        }

        return $nodes;
    }

    /**
     * @param ElementDescriptor|null $value
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

        $modelFactory = $this->getGraphQlService()->getModelFactory();
        $listClass = 'Pimcore\\Model\\DataObject\\' . ucfirst($this->class->getName()) . '\\Listing';
        /** @var Listing\Concrete $objectList */
        $objectList = $modelFactory->build($listClass);
        $tableName = $objectList->getDao()->getTableName();

        $conditionParts = [];
        $db = Db::get();
        if (isset($args['ids'])) {
            // Explode it and then quote it
            if (!is_array($args['ids'])) {
                $args['ids'] = explode(',', $args['ids']);
            }
            $ids = implode(', ', array_map([$db, 'quote'], $args['ids']));
            $conditionParts[] = sprintf('(%s IN (' . $ids . '))', Service::getVersionDependentDatabaseColumnName('o_id'));
        }
        if (isset($args['fullpaths'])) {
            $quotedFullpaths = array_map(
                static function ($fullpath) use ($db) {
                    $fullpath = trim($fullpath, " '");
                    $fullpath = Service::correctPath($fullpath);

                    return $db->quote($fullpath);
                },
                str_getcsv($args['fullpaths'], ',', "'")
            );
            $conditionParts[] = sprintf('(CONCAT(`%s`,`%s`) IN (' . implode(',', $quotedFullpaths) . '))',
                Service::getVersionDependentDatabaseColumnName('o_path'),
                Service::getVersionDependentDatabaseColumnName('o_key'));
        }

        if (isset($args['tags'])) {
            if (!is_array($args['tags'])) {
                $args['tags'] = explode(',', $args['tags']);
            }
            $tags = strtolower(implode(', ', array_map(static function ($tag) use ($db) {
                $tag = trim($tag);

                return $db->quote($tag);
            }, $args['tags'])));

            $conditionParts[] = "o_id IN (
                            SELECT cId FROM tags_assignment INNER JOIN tags ON tags.id = tags_assignment.tagid
                            WHERE
                                ctype = 'object' AND LOWER(tags.name) IN (" . $tags . '))';
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

        /** @var Configuration $configuration */
        $configuration = $context['configuration'];
        $sqlListCondition = $configuration->getSqlObjectCondition();

        if ($sqlListCondition) {
            $conditionParts[] = '(' . $sqlListCondition . ')';
        }

        if (!$configuration->skipPermisssionCheck()) {
            // check permissions
            $workspacesTableName = 'plugin_datahub_workspaces_object';
            $conditionParts[] = sprintf(' (
            (
                SELECT `read` from ' . $db->quoteIdentifier($workspacesTableName) . '
                WHERE ' . $db->quoteIdentifier($workspacesTableName) . '.configuration = ' . $db->quote($configuration->getName()) . '
                AND LOCATE(CONCAT(' . $db->quoteIdentifier($tableName) . '.%s,' . $db->quoteIdentifier($tableName) . '.%s),' . $db->quoteIdentifier($workspacesTableName) . '.cpath)=1
                ORDER BY LENGTH(' . $db->quoteIdentifier($workspacesTableName) . '.cpath) DESC
                LIMIT 1
            )=1
            OR
            (
                SELECT `read` from ' . $db->quoteIdentifier($workspacesTableName) . '
                WHERE ' . $db->quoteIdentifier($workspacesTableName) . '.configuration = ' . $db->quote($configuration->getName()) . '
                AND LOCATE(' . $db->quoteIdentifier($workspacesTableName) . '.cpath,CONCAT(' . $db->quoteIdentifier($tableName) . '.%s,' . $db->quoteIdentifier($tableName) . '.%s))=1
                ORDER BY LENGTH(' . $db->quoteIdentifier($workspacesTableName) . '.cpath) DESC
                LIMIT 1
            )=1
            )',
                Service::getVersionDependentDatabaseColumnName('o_path'),
                Service::getVersionDependentDatabaseColumnName('o_key'),
                Service::getVersionDependentDatabaseColumnName('o_path'),
                Service::getVersionDependentDatabaseColumnName('o_key'));
        }

        if (isset($args['filter'])) {
            $filter = json_decode($args['filter'], false);
            if (!$filter) {
                throw new ClientSafeException('unable to decode filter');
            }

            $className = $this->class->getName();
            $columns = $this->configuration->configuration['schema']['queryEntities'][$className]['columnConfig']['columns'];

            Helper::addJoins($objectList, $filter, $columns, $mappingTable);

            $filterCondition = Helper::buildSqlCondition($tableName, $filter, null, null, $mappingTable);
            $conditionParts[] = $filterCondition;
        }

        $condition = implode(' AND ', $conditionParts);
        $objectList->setCondition($condition);

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
     * @param ElementDescriptor|null $value
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

    /**
     * @param bool $isFullpathSet
     * @param bool $isIdSet
     * @param array $args
     *
     * @return string
     */
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
