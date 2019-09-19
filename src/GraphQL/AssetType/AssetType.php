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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\AssetType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class AssetType extends ObjectType
{
    use ServiceTrait;

    protected $fieldname;

    /**
     * @var AssetMetadataItem
     */
    protected $assetMetadataItemType;

    /**
     * AssetType constructor.
     * @param Service $graphQlService
     * @param AssetMetadataItem $assetMetadataItemType
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, AssetMetadataItem $assetMetadataItemType, $config = ["name" => "asset"], $context = [])
    {
        $this->setGraphQLService($graphQlService);
        $this->assetMetadataItemType = $assetMetadataItemType;
        $this->build($config);
        parent::__construct($config);
    }


    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\AssetType();
        $resolver->setGraphQLService($this->getGraphQlService());

        $config['fields'] = [
            'creationDate' => Type::int(),
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
            'mimetype' => Type::string(),

            'modificationDate' => Type::int(),
            'type' => Type::string(),
            'filesize' => Type::int(),
            'data' => [
                'type' => Type::string(),
                'args' => [
                    'thumbnail' => ['type' => Type::string()]

                ]
            ],
            'metadata' => [
                'type' => Type::listOf($this->assetMetadataItemType),
                'resolve' => [$resolver, "resolveMetadata"]
            ]
        ];
    }

    /**
     * @return string
     */
    public function getFieldname(): string
    {
        return $this->fieldname;
    }

    /**
     * @param string $fieldname
     */
    public function setFieldname(string $fieldname): void
    {
        $this->fieldname = $fieldname;
    }
}
