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
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\ListingEvent;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\NotAllowedException;
use Pimcore\Bundle\DataHubBundle\GraphQL\Helper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\PermissionInfoTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\AbstractFieldHelper;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Document;
use Pimcore\Model\Listing\AbstractListing;
use Pimcore\Model\Property;
use Pimcore\Model\Element\Service as ElementService;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;


class Element
{

    use ServiceTrait;
    use PermissionInfoTrait;

    /** @var string */
    protected $elementType;
    /**
     * @var null
     */
    protected $configuration;

    public function __construct(string $elementType, Service $graphQlService, $configuration = null, $omitPermissionCheck = false)
    {
        $this->elementType = $elementType;
        $this->configuration = $configuration;
        $this->omitPermissionCheck = $omitPermissionCheck;
        $this->setGraphQLService($graphQlService);
    }

    /**
     * @param array            $value
     * @param array            $args
     * @param array            $context
     * @param ResolveInfo|null $resolveInfo
     * @return array|Property[]|null
     * @throws ClientSafeException
     */
    public function resolveProperties(array $value = null, array $args = [], array $context, ResolveInfo $resolveInfo = null)
    {
        $elementId = $value["id"];
        $element = ElementService::getElementById($this->elementType, $elementId);

        if (!$element) {
            throw new ClientSafeException("element " . $this->elementType . " " . $elementId . " not found");
        }

        if (isset($args['keys'])) {
            $result = [];
            $properties = $element->getProperties();
            /** @var Property $property */
            foreach ($properties as $property) {
                if (in_array($property->getName(), $args['keys'])) {
                    $result[] = $property;
                }
            }
        } else {
            $result = $element->getProperties();
        }

        return $result;
    }

    /**
     * @param array $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveParent($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $element = ElementService::getElementById($this->elementType, $value['id']);
        if ($element) {
            $parent = $element->getParent();
            if ($parent) {
                return $this->extractSingleElement($parent, $args, $context, $resolveInfo);
            }
        }
        return null;
    }

    /**
     * @param array $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveChildren($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $element = ElementService::getElementById($this->elementType, $value['id']);
        if ($element) {
            $arguments = $this->composeArguments($args);
            return $this->extractMultipleElements($element->getChildren(...$arguments), $args, $context, $resolveInfo);
        }
        return [];
    }

    /**
     * @param array $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveSiblings($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $element = ElementService::getElementById($this->elementType, $value['id']);
        if ($element) {
            $arguments = $this->composeArguments($args);
            return $this->extractMultipleElements($element->getSiblings(...$arguments), $args, $context, $resolveInfo);
        }
        return [];
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
        $element = $value['node'];

        if (null !== $element) {
            return $this->extractSingleElement($element, $args, $context, $resolveInfo);
        }

        return null;
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
        $listClass = $this->getListType();

        /** @var AbstractListing $objectList */
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
//        if (isset($args['published']) && $args['published'] === false) {
//            $objectList->setUnpublished(true);
//        }

        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
//        $sqlListCondition = $configuration->getSql();
//
//        if ($sqlListCondition) {
//            $conditionParts[] = '(' . $sqlListCondition . ')';
//        }

        // check permissions
        $db = Db::get();
        $tableName = $this->getTypeTableName();
        $workspacesTableName = 'plugin_datahub_workspaces_' . $this->elementType;
        $conditionParts[] = ' (
            (
                SELECT `read` from ' . $db->quoteIdentifier($workspacesTableName) . '
                WHERE ' . $db->quoteIdentifier($workspacesTableName) . '.configuration = ' . $db->quote($configuration->getName()) . '
                AND LOCATE(CONCAT(' . $db->quoteIdentifier($tableName) . '.path,' . $db->quoteIdentifier($tableName) . '.filename),' . $db->quoteIdentifier($workspacesTableName) . '.cpath)=1
                ORDER BY LENGTH(' . $db->quoteIdentifier($workspacesTableName) . '.cpath) DESC
                LIMIT 1
            )=1
            OR
            (
                SELECT `read` from ' . $db->quoteIdentifier($workspacesTableName) . '
                WHERE ' . $db->quoteIdentifier($workspacesTableName) . '.configuration = ' . $db->quote($configuration->getName()) . '
                AND LOCATE(' . $db->quoteIdentifier($workspacesTableName) . '.cpath,CONCAT(' . $db->quoteIdentifier($tableName) . '.filename,' . $db->quoteIdentifier($tableName) . '.filename))=1
                ORDER BY LENGTH(' . $db->quoteIdentifier($workspacesTableName) . '.cpath) DESC
                LIMIT 1
            )=1
        )';

        if (isset($args['filter'])) {
            $filter = json_decode($args['filter'], false);
            if (!$filter) {
                throw new ClientSafeException('unable to decode filter');
            }
            $filterCondition = Helper::buildSqlCondition($tableName, $filter);
            $conditionParts[] = $filterCondition;
        }

        if ($conditionParts) {
            $condition = implode(' AND ', $conditionParts);
            $objectList->setCondition($condition);
        }

//        $event =  new ListingEvent(
//            $objectList,
//            $args,
//            $context,
//            $resolveInfo
//        );
//        $this->eventDispatcher->dispatch(ListingEvents::PRE_LOAD, $event);
//        $objectList = $event->getListing();

        $totalCount = $objectList->getTotalCount();
        $objectList = $objectList->load();

        $nodes = [];

        foreach ($objectList as $element) {
            $nodes[] = [
                'cursor' => $this->elementType . '-' . $element->getId(),
                'node' => $element,
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
     * @param array $args
     * @return array
     */
    protected function composeArguments($args = [])
    {
        $arguments = [];
        if ($this->elementType === 'object') {
            $arguments[] = isset($args['objectTypes']) ? $args['objectTypes'] : [AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_FOLDER];
        }
        return $arguments;
    }

    /**
     * @param array $elements
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    protected function extractMultipleElements($elements, $args, $context, $resolveInfo)
    {
        $result = [];
        if ($elements) {
            foreach ($elements as $element) {
                $result[] = $this->extractSingleElement($element, $args, $context, $resolveInfo);
            }
        }
        return array_filter($result);
    }

    /**
     * @param Element $element
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    protected function extractSingleElement($element, $args, $context, $resolveInfo)
    {
        // Check Workspace permissions
        if (!WorkspaceHelper::isAllowed($element, $context['configuration'], 'read')) {
            if (PimcoreDataHubBundle::getNotAllowedPolicy() == PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION) {
                throw new NotAllowedException('not allowed to view ' . $element->getFullPath());
            } else {
                return null;
            }
        }

        $data = new ElementDescriptor($element);
        $data['id'] = $element->getId();

        // Check element type
        $treeType = $this->getTreeType();
        $elementType = $treeType->resolveType($data, $context, $resolveInfo);
        if (in_array($elementType, $treeType->getTypes(), true)) {
            $this->getFieldHelper()->extractData($data, $element, $args, $context, $resolveInfo);
            return $data;
        }
        return null;
    }

    /**
     * @return UnionType|null
     */
    protected function getTreeType()
    {
        switch ($this->elementType) {
            case 'asset':
                return $this->getGraphQlService()->buildGeneralType('asset_tree');
            case 'document':
                return $this->getGraphQlService()->buildGeneralType('document_tree');
            case 'object':
                return $this->getGraphQlService()->buildGeneralType('object_tree');
            default:
                trigger_error("unknown element type");
        }
        return null;
    }

    /**
     * @return ?string
     */
    protected function getListType()
    {
        switch ($this->elementType) {
            case 'asset':
                return Asset\Listing::class;
            case 'document':
                return Document\Listing::class;
            default:
                trigger_error("unknown element type");
        }

        return null;
    }

    /**
     * @return ?string
     */
    protected function getTypeTableName()
    {
        switch ($this->elementType) {
            case 'asset':
                return 'assets';
            case 'document':
                return 'documents';
            default:
                trigger_error("unknown element type");
        }

        return null;
    }

    /**
     * @return AbstractFieldHelper|null
     */
    protected function getFieldHelper()
    {
        switch ($this->elementType) {
            case 'asset':
                return $this->getGraphQLService()->getAssetFieldHelper();
            case 'document':
                return $this->getGraphQLService()->getDocumentFieldHelper();
            case 'object':
                return $this->getGraphQLService()->getObjectFieldHelper();
            default:
                trigger_error("unknown element type");
        }
        return null;
    }
}
