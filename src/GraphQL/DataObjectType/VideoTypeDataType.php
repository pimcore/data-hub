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

use GraphQL\Deferred;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\GraphQL\AssetType\AssetType;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

/**
 * @internal
 */
final class VideoTypeDataType extends UnionType
{
    use ServiceTrait;

    /** @var AssetType */
    protected $assetType;

    public function __construct(Service $graphQlService)
    {
        $this->setGraphQLService($graphQlService);

        // @phpstan-ignore-next-line - We can't define the types in the constructor because the `getTypes` method is overwritten
        parent::__construct(['name' => 'VideoData']);
    }

    /**
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
                        'id' => ['type' => Type::string(), 'description' => 'external ID'],
                    ],
                ]
            ),
            $this->assetType,
        ];
    }

    public function resolveType($element, $context, ResolveInfo $info): ObjectType|string|callable|Deferred|null
    {
        if ($element instanceof ElementDescriptor) {
            return $this->assetType;
        }

        return $info->schema->getType('VideoDataDescriptor');
    }
}
