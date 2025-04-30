<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\PropertyType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Element\Data\MarkerHotspotItem;
use Pimcore\Model\Property;

/**
 * @internal
 */
final class DataObjectType extends ObjectType
{
    use ServiceTrait;

    public function __construct(Service $graphQlService, ObjectsType $objectUnionType)
    {
        $this->graphQlService = $graphQlService;

        $config = [
            'name' => 'property_object',
            'fields' => [
                'name' => [
                    'type' => Type::string(),
                    'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                        if ($value instanceof MarkerHotspotItem || $value instanceof Property) {
                            return $value->getName();
                        }
                    },
                ],
                'type' => [
                    'type' => Type::string(),
                    'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                        if ($value instanceof MarkerHotspotItem || $value instanceof Property) {
                            return $value->getType();
                        }
                    },
                ],
                'object' => [
                    'type' => $objectUnionType,
                    'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) use ($graphQlService) {
                        if ($value instanceof MarkerHotspotItem || $value instanceof Property) {
                            if ($value instanceof MarkerHotspotItem) {
                                $element = \Pimcore\Model\Element\Service::getElementById($value->getType(), $value->getValue());
                            } else {
                                $element = $value->getData();
                            }

                            if ($element) {
                                if (!WorkspaceHelper::checkPermission($element, 'read')) {
                                    return null;
                                }

                                $data = new ElementDescriptor($element);
                                $graphQlService->extractData($data, $element, $args, $context, $resolveInfo);

                                return $data;
                            }
                        }

                        return null;
                    },
                ],
            ],
        ];

        parent::__construct($config);
    }
}
