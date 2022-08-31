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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\General\FolderType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class ObjectFolderType extends FolderType
{
    /**
     * @param Service $graphQlService
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
                'type' => Type::string()
            ],
            'creationDate' => Type::int(),
            'modificationDate' => Type::int(),
            'parent' => [
                'type' => $objectTreeType,
                'resolve' => [$resolver, 'resolveParent'],
            ],
            'index' => [
                'type' => Type::int(),
                'resolve' => [$resolver, 'resolveIndex']
            ],
            'childrenSortBy' => [
                'type' => Type::string(),
                'resolve' => [$resolver, 'resolveChildrenSortBy']
            ],
            'children' => [
                'type' => Type::listOf($objectTreeType),
                'args' => [
                    'objectTypes' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'list of object types (object, variant, folder)'
                    ],
                ],
                'resolve' => [$resolver, 'resolveChildren'],
            ],
            'properties' => [
                'type' => Type::listOf($propertyType),
                'args' => [
                    'keys' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'comma separated list of key names'
                    ]
                ],
                'resolve' => [$resolver, 'resolveProperties']
            ],
            '_siblings' => [
                'type' => Type::listOf($objectTreeType),
                'args' => [
                    'objectTypes' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'list of object types (object, variant, folder)'
                    ],
                ],
                'resolve' => [$resolver, 'resolveSiblings'],
            ],
        ];
    }
}
