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
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Asset;

class AssetType extends ObjectType
{
    use ServiceTrait;

    /**
     * @var string
     */
    protected $fieldname;

    /**
     * @param Service $graphQlService
     * @param array   $config
     * @param array   $context
     *
     * @throws \Exception
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'asset'], $context = [])
    {
        $this->setGraphQLService($graphQlService);
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param $config
     *
     * @throws \Exception
     */
    public function build(&$config)
    {
        $resolver = new Resolver\AssetType();
        $resolver->setGraphQLService($this->getGraphQlService());

        $service = $this->getGraphQlService();
        $assetTree = $service->buildAssetType('asset_tree');
        $assetMetadataItemType = $service->buildAssetType('asset_metadataitem');

        $propertyType = $this->getGraphQlService()->buildGeneralType('element_property');
        $elementResolver = new Resolver\Element('asset', $this->getGraphQlService());

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

                ],
                'resolve' => function($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                    return $this->resolveAssetPath($value, $args, $context, $resolveInfo, false);
                }
            ],
            'mimetype' => Type::string(),
            'modificationDate' => Type::int(),
            'type' => Type::string(),
            'filesize' => Type::int(),
            'data' => [
                'type' => Type::string(),
                'args' => [
                    'thumbnail' => ['type' => Type::string()]
                ],
                'resolve' => function($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                    return $this->resolveAssetPath($value, $args, $context, $resolveInfo, true);
                }
            ],
            'metadata' => [
                'type' => Type::listOf($assetMetadataItemType),
                'resolve' => [$resolver, 'resolveMetadata']
            ],
            'properties' => [
                'type' => Type::listOf($propertyType),
                'args' => [
                    'keys' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'comma separated list of key names'
                    ]
                ],
                'resolve' => [$elementResolver, "resolveProperties"]
            ],
            'parent' => [
                'type' => $assetTree,
                'resolve' => [$elementResolver, "resolveParent"],
            ],
            'children' => [
                'type' => Type::listOf($assetTree),
                'resolve' => [$elementResolver, "resolveChildren"],
            ],
            '_siblings' => [
                'type' => Type::listOf($assetTree),
                'resolve' => [$elementResolver, "resolveSiblings"],
            ],
        ];
    }

    /**
     * @param mixed       $value
     * @param array       $args
     * @param array       $context
     * @param ResolveInfo $resolveInfo
     * @param bool        $resolveForData
     *
     * @return string|null
     * @throws \Exception
     */
    protected function resolveAssetPath($value, $args, $context, ResolveInfo $resolveInfo, bool $resolveForData = false)
    {
        if (!$value instanceof ElementDescriptor) {
            return null;
        }

        $asset = Asset::getById($value['id']);

        if (!WorkspaceHelper::isAllowed($asset, $context['configuration'], 'read')) {
            if (PimcoreDataHubBundle::getNotAllowedPolicy() === PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION) {
                throw new \Exception('not allowed to view asset');
            } else {
                return null;
            }
        }

        if ($asset instanceof Asset\Image || $asset instanceof Asset\Video) {
            if ($resolveForData === false) {
                return isset($args['thumbnail']) ? $asset->getThumbnail($args['thumbnail'], false) : $asset->getFullPath();
            } else {
                return isset($args['thumbnail'])
                    ? base64_encode(file_get_contents($asset->getThumbnail($args['thumbnail'], false)->getFileSystemPath()))
                    : base64_encode(file_get_contents($asset->getFileSystemPath()));
            }
        } elseif ($asset instanceof Asset\Document) {
            if ($resolveForData === false) {
                return isset($args['thumbnail']) ? $asset->getImageThumbnail($args['thumbnail']) : $asset->getFullPath();
            } else {
                return isset($args['thumbnail'])
                    ? base64_encode(file_get_contents($asset->getImageThumbnail($args['thumbnail'])->getFileSystemPath()))
                    : base64_encode(file_get_contents($asset->getFileSystemPath()));
            }
        } elseif ($asset instanceof Asset) {
            return $resolveForData === false ? $asset->getFullPath() : base64_encode(file_get_contents($asset->getFileSystemPath()));
        }

        return null;
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
