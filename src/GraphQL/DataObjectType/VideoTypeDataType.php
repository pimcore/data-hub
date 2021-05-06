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

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\GraphQL\AssetType\AssetType;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class VideoTypeDataType extends UnionType
{
    use ServiceTrait;

    /** @var AssetType */
    protected $assetType;

    public function __construct(Service $graphQlService)
    {
        $config['name'] = 'VideoData';
        $this->setGraphQLService($graphQlService);
        parent::__construct($config);
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function getTypes(): array
    {
        // why not just use scalars ?
        // https://kamranicus.com/posts/2018-07-02-handling-multiple-scalar-types-in-graphql
        $service = $this->getGraphQlService();
        $this->assetType = $service->buildAssetType('asset');

        return [
            new ObjectType([
                'name' => 'VideoDataDescriptor',
                'fields' => [
                        'id' => ['type' => Type::string(), 'description' => 'external ID']
                    ]
                ]
            ),
            $this->assetType
        ];
    }

    /**
     * @inheritdoc
     */
    public function resolveType($element, $context, ResolveInfo $info)
    {
        if ($element instanceof ElementDescriptor) {
            return $this->assetType;
        } else {
            $descriptorType = $info->schema->getType('VideoDataDescriptor');

            return $descriptorType;
        }

        return null;
    }
}
