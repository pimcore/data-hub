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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationFieldConfigGenerator;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class ImageGallery extends Base
{
    /** {@inheritdoc } */
    public function getGraphQlMutationFieldConfig($nodeDef, $class, $container = null, $params = [])
    {
        $processor = new \Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor\ImageGallery($nodeDef);
        $processor->setGraphQLService($this->getGraphQlService());

        $dataTypes = $this->getGraphQlService()->getDataObjectDataTypes();

        $imageInput = array_key_exists('gallery_image_input', $dataTypes)
            ? $dataTypes['gallery_image_input']
            : new InputObjectType([
                'name' => 'GalleryImageInput',
                'fields' => [
                    'id' => Type::int(),
                ],
            ]);

        $inputType = array_key_exists('image_gallery_input', $dataTypes)
            ? $dataTypes['image_gallery_input']
            : new InputObjectType([
                'name' => 'ImageGalleryInput',
                'fields' => [
                    'replace' => [
                        'type' => Type::boolean(),
                        'description' => 'if true then the entire gallery list will be overwritten',
                    ],
                    'images' => [
                        'type' => Type::listOf($imageInput),
                    ],
                ],
            ]);


        if (!array_key_exists('gallery_image_input', $dataTypes) && !array_key_exists('image_gallery_input', $params) ) {
            $newDataTypes = [
                'gallery_image_input' => $imageInput,
                'image_gallery_input' => $inputType
            ];

            $this->getGraphQlService()->registerDataObjectDataTypes($dataTypes + $newDataTypes);
        }

        return [
            'arg' => $inputType,
            'processor' => [$processor, 'process'],
        ];
    }
}
