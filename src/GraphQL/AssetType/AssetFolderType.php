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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\AssetType;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\General\FolderType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class AssetFolderType extends FolderType
{
    /**
     * @param Service $graphQlService
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $config = [], $context = [])
    {
        parent::__construct($graphQlService, ['name' => 'asset_folder'], $context);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $propertyType = $this->getGraphQlService()->buildGeneralType('element_property');
        $assetTree = $this->getGraphQlService()->buildGeneralType('asset_tree');
        $elementResolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Element('asset', $this->getGraphQlService());

        $config['fields'] = [
            'id' => ['name' => 'id',
                'type' => Type::id(),
            ],
            'filename' => Type::string(),
            'fullpath' => [
                'type' => Type::string(),
                'args' => [
                    'thumbnail' => ['type' => Type::string()]

                ]
            ],
            'creationDate' => Type::int(),
            'modificationDate' => Type::int(),
            'properties' => [
                'type' => Type::listOf($propertyType),
                'args' => [
                    'keys' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'comma seperated list of key names'
                    ]
                ],
                'resolve' => [$elementResolver, 'resolveProperties']
            ],
            'parent' => [
                'type' => $this,
                'resolve' => [$elementResolver, 'resolveParent'],
            ],
            'children' => [
                'type' => Type::listOf($assetTree),
                'resolve' => [$elementResolver, 'resolveChildren'],
            ],
            '_siblings' => [
                'type' => Type::listOf($assetTree),
                'resolve' => [$elementResolver, 'resolveSiblings'],
            ],
        ];
    }
}
