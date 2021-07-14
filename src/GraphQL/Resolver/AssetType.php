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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ElementTagTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Asset;

class AssetType
{
    use ServiceTrait, ElementTagTrait;

    /**
     * @param array $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array
     *
     * @throws \Exception
     */
    public function resolveTag($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $asset = $this->getAssetFromValue($value, $context);

        if ($asset) {
            $result = $this->getTags('asset', $asset->getId());
            if ($result) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @param array $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array
     *
     * @throws \Exception
     */
    public function resolveMetadata($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $asset = $this->getAssetFromValue($value, $context);

        if ($asset) {
            $metadata = $asset->getObjectVar('metadata');
            if ($metadata) {
                if (isset($args['ignore_language']) && $args['ignore_language']) {
                    return $metadata;
                }

                $map = [];
                $keys = [];
                $language = isset($args['language']) ? $args['language'] : $this->getGraphQlService()->getLocaleService()->findLocale();

                foreach ($metadata as $item) {
                    $keys[$item['name']] = 1;
                    $l = $item['language'] ? $item['language'] : 'default';
                    $map[$l][$item['name']] = $item;
                }
                $result = [];

                foreach ($keys as $key => $found) {
                    if (isset($map[$language][$key])) {
                        $result[] = $map[$language][$key];
                    } elseif (isset($map['default'][$key])) {
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
     *
     * @throws \Exception
     */
    public function resolvePath($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $asset = $this->getAssetFromValue($value, $context);

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
     *
     * @throws \Exception
     */
    public function resolveData($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $asset = $this->getAssetFromValue($value, $context);

        if ($asset instanceof Asset\Image || $asset instanceof Asset\Video) {
            $data = stream_get_contents($asset->getStream());

            return isset($args['thumbnail'])
                ? base64_encode(stream_get_contents($asset->getThumbnail($args['thumbnail'],
                    false)->getStream()))
                : base64_encode(stream_get_contents($asset->getStream()));
        } elseif ($asset instanceof Asset\Document) {
            return isset($args['thumbnail'])
                ? base64_encode(stream_get_contents($asset->getImageThumbnail($args['thumbnail'])->getStream()))
                : base64_encode(stream_get_contents($asset->getStream()));
        } elseif ($asset instanceof Asset) {
            return base64_encode(stream_get_contents($asset->getStream()));
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
     *
     * @throws \Exception
     */
    public function resolveSrcSet($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $asset = $this->getAssetFromValue($value, $context);

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
     *
     * @throws \Exception
     */
    public function resolveResolutions($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $types = $args['types'];
        $thumbnail = $value['url'] ?? null;

        $asset = null;
        if ($thumbnail instanceof Asset\Image\Thumbnail) {
            $resolutions = [];
            $thumbnailName = $thumbnail->getConfig()->getName();
            $asset = $thumbnail->getAsset();
            if (!WorkspaceHelper::checkPermission($asset, 'read')) {
                return null;
            }

            $thumbnail = $asset->getThumbnail($thumbnailName, false);
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
            $thumbnailName = $args['thumbnail'];
            $asset = $this->getAssetFromValue($value, $context);
            /** @var Asset\Image\Thumbnail $thumbnail */
            $thumbnail = $asset->getThumbnail($thumbnailName, false);
            $thumbnailConfig = $thumbnail->getConfig();
            $resolutions = [];
            foreach ($types as $type) {
                $thumbConfigRes = clone $thumbnailConfig;
                $thumbConfigRes->setHighResolution($type);
                $thumbConfigRes->setMedias([]);
                $resolutions[] = [
                    'url' => $asset->getThumbnail($thumbConfigRes, false),
                    'resolution' => $type,
                ];
            }

            return $resolutions;
        }

        return [];
    }

    /**
     * @param mixed $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo $resolveInfo
     *
     * @return array
     *
     * @throws \Exception
     */
    public function resolveDimensions($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof ElementDescriptor) {
            $thumbnailName = $args['thumbnail'] ?? null;

            /**
             * @var Asset\Image $asset
             */
            $asset = $this->getAssetFromValue($value, $context);

            if (!$asset instanceof Asset\Image) {
                return null;
            }

            if (!$thumbnailName) {
                return [
                    'width' => $asset->getWidth(),
                    'height' => $asset->getHeight(),
                ];
            }

            $thumbnail = $asset->getThumbnail($thumbnailName, false);

            $width = $thumbnail->getWidth();
            $height = $thumbnail->getHeight();

            return [
                'width' => $width,
                'height' => $height
            ];
        }

        return [];
    }

    /**
     * @param mixed       $value
     * @param array       $context
     *
     * @return Asset|null
     *
     * @throws \Exception
     */
    protected function getAssetFromValue($value, $context)
    {
        if (!$value instanceof ElementDescriptor) {
            return null;
        }

        $asset = Asset::getById($value['id']);

        if (!WorkspaceHelper::checkPermission($asset, 'read')) {
            return null;
        }

        return $asset;
    }
}
