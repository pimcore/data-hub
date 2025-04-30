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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\General\FolderType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

/**
 * @internal
 */
final class ObjectFolderType extends FolderType
{
    /**
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $config = [], $context = [])
    {
        parent::__construct($graphQlService, ['name' => 'object_folder'], $context);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $propertyType = $this->getGraphQlService()->buildGeneralType('element_property');
        $objectTreeType = $this->getGraphQlService()->buildGeneralType('object_tree');

        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\DataObject($this->getGraphQLService());

        $config['fields'] = [
            'id' => [
                'name' => 'id',
                'type' => Type::id(),
            ],
            'key' => Type::string(),
            'fullpath' => [
                'type' => Type::string(),
            ],
            'creationDate' => [
                'type' => Type::string(),
                'resolve' => [$resolver, 'resolveCreationDate'],
            ],
            'modificationDate' => [
                'type' => Type::string(),
                'resolve' => [$resolver, 'resolveModificationDate'],
            ],
            'parent' => [
                'type' => $objectTreeType,
                'resolve' => [$resolver, 'resolveParent'],
            ],
            'index' => [
                'type' => Type::int(),
                'resolve' => [$resolver, 'resolveIndex'],
            ],
            'childrenSortBy' => [
                'type' => Type::string(),
                'resolve' => [$resolver, 'resolveChildrenSortBy'],
            ],
            'children' => [
                'type' => Type::listOf($objectTreeType),
                'args' => [
                    'objectTypes' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'list of object types (object, variant, folder)',
                    ],
                ],
                'resolve' => [$resolver, 'resolveChildren'],
            ],
            'properties' => [
                'type' => Type::listOf($propertyType),
                'args' => [
                    'keys' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'comma separated list of key names',
                    ],
                ],
                'resolve' => [$resolver, 'resolveProperties'],
            ],
            '_siblings' => [
                'type' => Type::listOf($objectTreeType),
                'args' => [
                    'objectTypes' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'list of object types (object, variant, folder)',
                    ],
                ],
                'resolve' => [$resolver, 'resolveSiblings'],
            ],
        ];
    }
}
