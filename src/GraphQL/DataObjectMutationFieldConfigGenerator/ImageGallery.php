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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationFieldConfigGenerator;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

/**
 * @internal
 */
final class ImageGallery extends Base
{
    /**
     * Shared across all image gallery fields: webonyx/graphql-php rejects a schema
     * containing several distinct type instances with the same name.
     */
    private static ?InputObjectType $inputType = null;

    /** {@inheritdoc } */
    public function getGraphQlMutationFieldConfig($nodeDef, $class, $container = null, $params = [])
    {
        $processor = new \Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor\ImageGallery($nodeDef);
        $processor->setGraphQLService($this->getGraphQlService());

        return [
            'arg' => self::getInputType(),
            'processor' => [$processor, 'process'],
        ];
    }

    private static function getInputType(): InputObjectType
    {
        if (self::$inputType === null) {
            $imageInput = new InputObjectType([
                'name' => 'GalleryImageInput',
                'fields' => [
                    'id' => Type::int(),
                ],
            ]);

            self::$inputType = new InputObjectType([
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
        }

        return self::$inputType;
    }
}
