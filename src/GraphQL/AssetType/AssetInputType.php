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

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementTag;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class AssetInputType extends InputObjectType
{
    use ServiceTrait;

    /**
     * @param Service $graphQlService
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'AssetInput'], $context = [])
    {
        $this->setGraphQLService($graphQlService);
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $config['fields'] = [
            'filename' => Type::string(),
            'parentId' => Type::int(),
            'data' => [
                'type' => Type::string(),
            ],
            'tags' => ElementTag::getElementTagInputTypeDefinition(),
            'metadata' => [
                'type' => Type::listOf(new InputObjectType([
                    'name' => 'MetadataItem',
                    'fields' => [
                        'name' => Type::nonNull(Type::string()),
                        'type' => Type::nonNull(Type::string()),
                        'data' => Type::string(),
                        'language' => Type::string()
                    ]
                ]))
            ]
        ];
    }
}
