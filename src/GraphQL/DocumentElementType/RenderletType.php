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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\Document\Editable\Renderlet;

/**
 * @internal
 */
final class RenderletType extends ObjectType
{
    /** @var static|null */
    protected static $instance;

    /**
     *
     * @return static
     *
     * @throws \Exception
     */
    public static function getInstance(Service $graphQlService)
    {
        if (!self::$instance) {
            $anyTargetType = $graphQlService->buildGeneralType('anytarget');

            $config =
                [
                    'name' => 'document_editableRenderlet',
                    'fields' => [
                        '_editableType' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                                if ($value instanceof Renderlet) {
                                    return $value->getType();
                                }
                            },
                        ],
                        '_editableName' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                                if ($value instanceof Renderlet) {
                                    return $value->getName();
                                }
                            },
                        ],
                        'id' => [
                            'type' => Type::int(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                                if ($value instanceof Renderlet) {
                                    return $value->getId();
                                }
                            },
                        ],
                        'type' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                                if ($value instanceof Renderlet) {
                                    return $value->getType();
                                }
                            },
                        ],
                        'subtype' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                                if ($value instanceof Renderlet) {
                                    return $value->getSubtype();
                                }
                            },
                        ],
                        'relation' => [
                            'type' => $anyTargetType,
                            'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) use ($graphQlService) {
                                if ($value instanceof Renderlet) {
                                    $target = $value->getO();
                                    if ($target) {
                                        $desc = new ElementDescriptor($target);
                                        $graphQlService->extractData($desc, $target, $args, $context, $resolveInfo);

                                        return $desc;
                                    }
                                }
                            },
                        ],
                    ],
                ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
