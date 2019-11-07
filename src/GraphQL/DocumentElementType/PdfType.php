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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\Document\Tag\Pdf;

class PdfType extends ObjectType
{
    protected static $instance;


    /**
     * @param Service $service
     * @return PdfType
     * @throws \Exception
     */
    public static function getInstance(Service $service)
    {
        if (!self::$instance) {

            $assetType = $service->buildAssetType("asset");

            $config =
                [
                    'name' => "document_tagPdf",
                    'fields' => [
                        '__tagName' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                                if ($value) {
                                    return $value->getName();
                                }
                            }
                        ],
                        '__tagType' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                                if ($value instanceof \Pimcore\Model\Document\Tag\Numeric) {
                                    return $value->getType();
                                }
                            }
                        ],
                        'pdf' => [
                            'type' => $assetType,
                            'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) use ($service) {
                                if ($value instanceof Pdf) {
                                    $pdfAsset = $value->getElement();
                                    if ($pdfAsset) {
                                        $data = new ElementDescriptor();
                                        $fieldHelper = $service->getAssetFieldHelper();
                                        $fieldHelper->extractData($data, $pdfAsset, $args, $context, $resolveInfo);
                                        $data['data'] = $data['data'] ? base64_encode($data['data']) : null;
                                        $data['__elementSubtype'] = $pdfAsset->getType();
                                        return $data;
                                    }
                                }
                            }
                        ]
                    ],
                ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
