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

        $imageInput = new InputObjectType([
            'name' => 'GalleryImageInput',
            'fields' => [
                'id' => Type::int()
            ]
        ]);

        $inputType = new InputObjectType([
            'name' => 'ImageGalleryInput',
            'fields' => [
                'replace' => [
                    'type' => Type::boolean(),
                    'description' => 'if true then the entire gallery list will be overwritten'
                ],
                'images' => [
                    'type' => Type::listOf($imageInput)
                ]
            ]
        ]);

        return [
            'arg' => $inputType,
            'processor' => [$processor, 'process']
        ];
    }
}
