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
                            'resolve' => self::resolveRenderlet(static fn (Renderlet $r) => $r->getType()),
                        ],
                        '_editableName' => [
                            'type' => Type::string(),
                            'resolve' => self::resolveRenderlet(static fn (Renderlet $r) => $r->getName()),
                        ],
                        'id' => [
                            'type' => Type::int(),
                            'resolve' => self::resolveRenderlet(static fn (Renderlet $r) => $r->getId()),
                        ],
                        'type' => [
                            'type' => Type::string(),
                            'resolve' => self::resolveRenderlet(static fn (Renderlet $r) => $r->getType()),
                        ],
                        'subtype' => [
                            'type' => Type::string(),
                            'resolve' => self::resolveRenderlet(static fn (Renderlet $r) => $r->getSubtype()),
                        ],
                        'relation' => [
                            'type' => $anyTargetType,
                            'resolve' => static function (
                                $value = null,
                                $args = [],
                                $context = [],
                                ?ResolveInfo $resolveInfo = null,
                            ) use ($graphQlService) {
                                if (!$value instanceof Renderlet) {
                                    return null;
                                }

                                $target = $value->getO();
                                if (!$target) {
                                    return null;
                                }

                                $desc = new ElementDescriptor($target);
                                $graphQlService->extractData($desc, $target, $args, $context, $resolveInfo);

                                return $desc;
                            },
                        ],
                    ],
                ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }

    /**
     * Wraps a field resolver so it only runs for a Renderlet editable.
     *
     * @param callable(Renderlet): mixed $resolver
     */
    private static function resolveRenderlet(callable $resolver): \Closure
    {
        return static function ($value = null) use ($resolver) {
            if ($value instanceof Renderlet) {
                return $resolver($value);
            }

            return null;
        };
    }
}
