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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentResolver\Link;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class LinkDataType extends ObjectType
{
    use ServiceTrait;

    /**
     * @param Service $graphQlService
     *
     * @throws \Exception
     */
    public function __construct(Service $graphQlService)
    {
        $this->graphQlService = $graphQlService;

        $anyTargetType = $graphQlService->buildGeneralType('anytarget');

        $config =
            [
                'name' => 'document_editableLink_data',
                'fields' => [
                    '_editableType' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getType();
                            }
                        }
                    ],
                    '_editableName' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getName();
                            }
                        }
                    ],
                    'internal' => [
                        'type' => Type::boolean(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getData()['internal'] ?? null;
                            }
                        }
                    ],
                    'internalType' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getData()['internalType'] ?? null;
                            }
                        }
                    ],
                    'internalId' => [
                        'type' => Type::int(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getData()['internalId'] ?? null;
                            }
                        }
                    ],
                    'path' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getData()['path'] ?? null;
                            }
                        }
                    ],
                    'text' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getData()['text'] ?? null;
                            }
                        }
                    ],
                    'target' => [
                        'type' => $anyTargetType,
                        'resolve' => [new Link($this->getGraphQlService()), 'resolveTarget']
                    ],
                    'windowTarget' => [ // Target is already in use.
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getData()['target'] ?? null;
                            }
                        }
                    ],
                    'parameters' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getData()['parameters'] ?? null;
                            }
                        }
                    ],
                    'anchor' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getData()['anchor'] ?? null;
                            }
                        }
                    ],
                    'title' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getData()['title'] ?? null;
                            }
                        }
                    ],
                    'accesskey' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getData()['accesskey'] ?? null;
                            }
                        }
                    ],
                    'relation' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getData()['rel'] ?? null;
                            }
                        }
                    ],
                    'tabindex' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getData()['tabindex'] ?? null;
                            }
                        }
                    ],
                    'class' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getData()['class'] ?? null;
                            }
                        }
                    ],
                    'attributes' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value instanceof \Pimcore\Model\Document\Editable\Link) {
                                return $value->getData()['attributes'] ?? null;
                            }
                        }
                    ],
                ]
            ];
        parent::__construct($config);
    }
}
