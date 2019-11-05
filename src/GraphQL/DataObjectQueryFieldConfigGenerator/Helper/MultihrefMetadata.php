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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Helper;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\ElementMetadata;
use Pimcore\Model\Document;
use Pimcore\Model\Element\Service;

class MultihrefMetadata
{
    use ServiceTrait;

    /**
     * @var
     */
    public $fieldDefinition;

    /**
     * @var
     */
    public $class;

    /**
     * @var
     */
    public $attribute;

    /**
     * MultihrefMetadata constructor.
     * @param \Pimcore\Bundle\DataHubBundle\GraphQL\Service $graphQlService
     * @param $attribute
     * @param $fieldDefinition
     * @param $class
     */
    public function __construct(\Pimcore\Bundle\DataHubBundle\GraphQL\Service $graphQlService, $attribute, $fieldDefinition, $class)
    {
        $this->fieldDefinition = $fieldDefinition;
        $this->class = $class;
        $this->attribute = $attribute;
        $this->setGraphQLService($graphQlService);
    }

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array|null
     *
     * @throws \Exception
     */
    public function resolve($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $relations = \Pimcore\Bundle\DataHubBundle\GraphQL\Service::resolveValue($value['id'], $this->fieldDefinition, $this->attribute, $args, $value);
        if ($relations) {
            /** @var $relation ElementMetadata */
            foreach ($relations as $relation) {
                $element = $relation->getElement();
                if (!WorkspaceHelper::isAllowed($element, $context['configuration'], 'read')) {
                    if (PimcoreDataHubBundle::getNotAllowedPolicy() == PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION) {
                        throw new \Exception('not allowed to view ' . $relation->getFullPath());
                    } else {
                        continue;
                    }
                }

                $data = new ElementDescriptor();
                $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();
                $fieldHelper->extractData($data, $relation, $args, $context, $resolveInfo);

                $element = $relation->getElement();
                $elementData = [];

                $type = Service::getType($element);
                if ($element instanceof Concrete) {
                    $subtype = $element->getClass()->getName();
                    $elementData['__elementType'] = $type;
                    $elementData['__elementSubtype'] = $subtype;
                } else if ($element instanceof Asset) {
                    $elementData['data'] = $elementData['data'] ? base64_encode(
                        $elementData['data']
                    ) : null;
                    $elementData['__elementType'] = 'asset';
                    $elementData['__elementSubtype'] = $element->getType();
                } else if ($relation instanceof Document) {
                    $data['id'] = $relation->getId();
                    $data['__elementType'] = $type;
                }

                $elementData['__relation'] = $relation;
                $elementData['__destId'] = $relation->getElementId();
                $data['element'] = $elementData;
                $data['metadata'] = microtime();

                $result[] = $data;
            }

            return $result;

        }

        return null;
    }
}
