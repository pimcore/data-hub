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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Asset;


class AssetType
{

    use ServiceTrait;

    private $thumbnail = null;

    /**
     * AssetType constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param array $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveMetadata($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $assetId = $value['id'];
        $asset = Asset::getById($assetId);
        if ($asset) {
            $metadata = $asset->getObjectVar('metadata');
            if ($metadata) {
                $map = [];
                $keys = [];
                $language = $this->getGraphQlService()->getLocaleService()->findLocale();

                foreach ($metadata as $item) {
                    $keys[$item['name']] = 1;
                    $l = $item['language'] ? $item['language'] : 'default';
                    $map[$l][$item['name']] = $item;
                }

                $result = [];

                foreach ($keys as $key => $found) {
                    if ($map[$language][$key]) {
                        $result[] = $map[$language][$key];
                    } elseif ($map['default'][$key]) {
                        $result[] = $map['default'][$key];
                    }
                }

                if ($result) {
                    return $result;
                }
            }
        }

        return null;

    }

    /**
     * @param mixed $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo $resolveInfo
     *
     * @return string|null
     * @throws \Exception
     */
    public function resolvePath($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
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

        if (isset($args['thumbnail'])) {
            $this->thumbnail = $args['thumbnail'];
        }

        if ($asset instanceof Asset\Image || $asset instanceof Asset\Video) {
            return isset($args['thumbnail']) ? $asset->getThumbnail($args['thumbnail'], false) : $asset->getFullPath();
        } elseif ($asset instanceof Asset\Document) {
            return isset($args['thumbnail']) ? $asset->getImageThumbnail($args['thumbnail']) : $asset->getFullPath();
        } elseif ($asset instanceof Asset) {
            return $asset->getFullPath();
        }
        return null;
    }

    /**
     * @param mixed $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo $resolveInfo
     *
     * @return string|null
     * @throws \Exception
     */
    public function resolveData($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
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

        if (isset($args['thumbnail'])) {
            $this->thumbnail = $args['thumbnail'];
        }

        if ($asset instanceof Asset\Image || $asset instanceof Asset\Video) {
            return isset($args['thumbnail'])
                ? base64_encode(file_get_contents($asset->getThumbnail($args['thumbnail'],
                    false)->getFileSystemPath()))
                : base64_encode(file_get_contents($asset->getFileSystemPath()));
        } elseif ($asset instanceof Asset\Document) {
            return isset($args['thumbnail'])
                ? base64_encode(file_get_contents($asset->getImageThumbnail($args['thumbnail'])->getFileSystemPath()))
                : base64_encode(file_get_contents($asset->getFileSystemPath()));
        } elseif ($asset instanceof Asset) {
            return base64_encode(file_get_contents($asset->getFileSystemPath()));
        }
        return null;
    }

    /**
     * @param mixed $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo $resolveInfo
     *
     * @return array
     * @throws \Exception
     */
    public function resolveSrcSet($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
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

        if (isset($args['thumbnail'])) {
            $this->thumbnail = $args['thumbnail'];
        }

        if ($asset instanceof Asset\Image) {
            $mediaQueries = [];
            $thumbnail = $asset->getThumbnail($args['thumbnail'], false);
            $thumbnailConfig = $asset->getThumbnailConfig($args['thumbnail']);
            if ($thumbnailConfig) {
                foreach ($thumbnailConfig->getMedias() as $key => $val) {
                    $mediaQueries[] = [
                        'descriptor' => $key,
                        'url' => $thumbnail->getMedia($key),
                    ];
                }
            }
            return $mediaQueries;
        }
        return null;
    }

    /**
     * @param mixed $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo $resolveInfo
     *
     * @return array
     * @throws \Exception
     */
    public function resolveResolutions($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $types = $args['types'];
        $thumbnail = $value['url'];

        if (empty($this->thumbnail)) {
            return [];
        }

        $asset = null;
        if ($thumbnail instanceof Asset\Image\Thumbnail) {
            $resolutions = [];
            if ($thumbnail->getConfig()->hasMedias()) {
                foreach ($types as $type) {
                    $key = $value['descriptor'];
                    $resolutions[] = [
                        'url' => $thumbnail->getMedia($key, $type),
                        'resolution' => $type,
                    ];
                }
            }
            return $resolutions;
        }

        if ($value instanceof ElementDescriptor) {
            $asset = Asset::getById($value['id']);
            $thumbnail = $asset->getThumbnail($this->thumbnail, false);
            $path = $thumbnail->getPath();
            $resolutions = [];
            foreach ($types as $type) {
                $url = preg_replace('/(.*)\.(.*)/i', '${1}@' . $type . 'x${2}', $path);
                $resolutions[] = [
                    'url' => $url,
                    'resolution' => $type,
                ];
            }
            return $resolutions;
        }

        return [];
    }

}

