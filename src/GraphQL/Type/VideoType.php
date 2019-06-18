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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class VideoType extends ObjectType
{

    use ServiceTrait;

    /**
     * @var AssetType
     */
    protected $assetType;

    /**
     * @var VideoTypeDataType
     */
    protected $videoDataType;


    /**
     * VideoType constructor.
     * @param Service $graphQlService
     * @param AssetType $assetType
     * @param VideoTypeDataType $videoDataType
     */
    public function __construct(Service $graphQlService, AssetType $assetType, VideoTypeDataType $videoDataType)
    {
        $this->setGraphQLService($graphQlService);
        $this->assetType = $assetType;
        $this->videoDataType = $videoDataType;
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Video();
        $resolver->setGraphQLService($this->getGraphQlService());

        $config['fields'] =
            [
                'type' => [
                    'type' => Type::string(),
                    'resolve' => [$resolver, "resolveType"]
                ],
                'data' => [
                    'type' => $this->videoDataType,
                    'resolve' => [$resolver, "resolveData"]
                ],
                'poster' => [
                    'type' => $this->assetType,
                    'resolve' => [$resolver, "resolvePoster"]
                ],
                'title' => [
                    'type' => Type::string(),
                    'resolve' => [$resolver, "resolveTitle"]
                ],
                'description' => [
                    'type' => Type::string(),
                    'resolve' => [$resolver, "resolveDescription"]
                ],

            ];
    }
}
