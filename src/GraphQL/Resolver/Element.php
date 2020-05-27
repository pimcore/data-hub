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
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\PermissionInfoTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\AbstractFieldHelper;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Property;
use Pimcore\Model\Element\Service as ElementService;


class Element
{

    use ServiceTrait;

    /** @var string */
    protected $elementType;

    public function __construct(string $elementType, Service $graphQlService)
    {
        $this->elementType = $elementType;
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
