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
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\AbstractFieldHelper;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Document;
use Pimcore\Model\Property;
use Pimcore\Model\Element\AbstractElement;


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
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     */
    public function resolveProperties($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $elementId = $value["id"];
        $element = $this->getElementById($elementId);

        if (!$element) {
            throw new \Exception("element " . $this->elementType . " " . $elementId . " not found");
        }

        if (isset($args['keys'])) {
            $result = [];
            $properties = $element->getProperties();
            /** @var $property Property */
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
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveParent($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $element = $this->getElementById($value['id']);
        if ($element) {
            $parent = $element->getParent();
            if ($parent) {
                return $this->extractSingleElement($parent, $args, $context, $resolveInfo);
            }
        }
        return null;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveChildren($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $element = $this->getElementById($value['id']);
        if ($element) {
            $arguments = $this->composeArguments($args);
            return $this->extractMultipleElements($element->getChildren(...$arguments), $args, $context, $resolveInfo);
        }
        return [];
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveSiblings($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $element = $this->getElementById($value['id']);
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
     * @param $value
     * @return AbstractElement|null
     * @throws \Exception
     */
    protected function getElementById($id)
    {
        switch ($this->elementType) {
            case 'asset':
                return Asset::getById($id);
            case 'document':
                return Document::getById($id);
            case 'object':
                return AbstractObject::getById($id);
            default:
                trigger_error("unknown element type");
        }
        return null;
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
        if (!WorkspaceHelper::isAllowed($element, $context['configuration'], 'read')) {
            if (PimcoreDataHubBundle::getNotAllowedPolicy() == PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION) {
                throw new \Exception('not allowed to view ' . $element->getFullPath());
            } else {
                return null;
            }
        }
        $data = [];
        $this->getFieldHelper()->extractData($data, $element, $args, $context, $resolveInfo);
        return $data;
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
