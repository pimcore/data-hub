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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper;

use GraphQL\Language\AST\FieldNode;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Asset\Video;

final class AssetFieldHelper extends AbstractFieldHelper
{
    public function getVideoThumbnail(Asset\Video $asset, string | Video\Thumbnail\Config $thumbNailConfig, ?string $thumbNailFormat = null): mixed
    {
        if (isset($thumbNailFormat) && $thumbNailFormat !== 'image') {
            $value = $asset->getThumbnail($thumbNailConfig);
            if ($value) {
                $formats = $value['formats'] ?? [];
                $format = $formats[$thumbNailFormat] ?? null;
                if ($format) {
                    return $format;
                }
            }
        } else {
            return $asset->getImageThumbnail($thumbNailConfig);
        }

        return null;
    }

    public function getImageDocumentThumbnail(
        Asset $asset,
        string | Image\Thumbnail\Config $thumbNailConfig,
        ?string $thumbNailFormat = null,
        bool $deferred = false,
        ?array $editableCrop = null
    ): mixed {
        $thumb = null;

        if ($asset instanceof Asset\Document) {
            $thumb = $asset->getImageThumbnail($thumbNailConfig, deferred: $deferred);
        }

        if ($asset instanceof Asset\Video) {
            $thumb = $asset->getImageThumbnail($thumbNailConfig);
        }

        if ($asset instanceof Asset\Image) {
            if ($editableCrop) {
                $thumbNailConfig = $this->getCroppedThumbnailConfig($asset, $thumbNailConfig, $editableCrop);
            }
            $thumb = $asset->getThumbnail($thumbNailConfig, $deferred);
        }

        if (
            !($asset instanceof Asset\Video) &&
            isset($thumb, $thumbNailFormat)
        ) {
            $thumb = $thumb->getAsFormat($thumbNailFormat);
        }

        return $thumb;
    }

    /**
     * Applies the crop selected in a document image editable to a thumbnail config,
     * the same way Document\Editable\Image does for the frontend.
     *
     * @param array{width: float|int, height: float|int, top: float|int, left: float|int} $crop percent values
     */
    public function applyEditableCrop(Image\Thumbnail\Config $thumbNailConfig, array $crop): Image\Thumbnail\Config
    {
        $thumbNailConfig = clone $thumbNailConfig;
        $cropConfig = [
            'width' => $crop['width'],
            'height' => $crop['height'],
            'y' => $crop['top'],
            'x' => $crop['left'],
        ];

        $thumbNailConfig->addItemAt(0, 'cropPercent', $cropConfig);
        foreach (array_keys($thumbNailConfig->getMedias()) as $mediaName) {
            $thumbNailConfig->addItemAt(0, 'cropPercent', $cropConfig, $mediaName);
        }
        $thumbNailConfig->generateAutoName();

        return $thumbNailConfig;
    }

    public function getCroppedThumbnailConfig(
        Asset\Image $asset,
        string | Image\Thumbnail\Config $thumbNailConfig,
        array $crop
    ): string | Image\Thumbnail\Config {
        if (is_string($thumbNailConfig)) {
            $thumbNailConfig = $asset->getThumbnail($thumbNailConfig)->getConfig() ?? $thumbNailConfig;
        }

        return $thumbNailConfig instanceof Image\Thumbnail\Config
            ? $this->applyEditableCrop($thumbNailConfig, $crop)
            : $thumbNailConfig;
    }

    public function getAssetThumbnail(
        Asset $asset,
        string | Image\Thumbnail\Config | Video\Thumbnail\Config $thumbNailConfig,
        ?string $thumbNailFormat = null,
        bool $deferred = false,
        ?array $editableCrop = null
    ): mixed {
        if (($asset instanceof Asset\Video) && (is_string($thumbNailConfig) || $thumbNailConfig instanceof Video\Thumbnail\Config)) {
            return $this->getVideoThumbnail($asset, $thumbNailConfig, $thumbNailFormat);
        } else {
            return $this->getImageDocumentThumbnail(
                $asset,
                $thumbNailConfig,
                $thumbNailFormat,
                $deferred,
                $editableCrop
            );
        }
    }

    /**
     * @param array $data
     * @param Asset $container
     * @param array $args
     * @param array $context
     * @param ResolveInfo $resolveInfo
     */
    public function doExtractData(FieldNode $ast, &$data, $container, $args, $context, $resolveInfo = null)
    {
        $astName = $ast->name->value;

        // sometimes we just want to expand relations just to throw them away afterwards because not requested
        if ($this->skipField($container, $astName)) {
            return;
        }

        $getter = 'get'.ucfirst($astName);
        $arguments = $this->getArguments($ast);
        $languageArgument = isset($arguments['language']) ? $arguments['language'] : null;
        $thumbnailArgument = isset($arguments['thumbnail']) ? $arguments['thumbnail'] : null;
        $thumbnailFormat = $arguments['format'] ?? null;

        $realName = $astName;

        if (($astName == 'fullpath' || $astName == 'data') && $thumbnailArgument && ($container instanceof Image || $container instanceof Video)) {
            if ($ast->alias) {
                // defer it
                $data[$realName] = function ($source, $args, $context, ResolveInfo $info) use ($container, $realName) {
                    if ($realName === 'fullpath') {
                        return $container->getThumbnail($args['thumbnail'], false);
                    }
                    if ($realName === 'data') {
                        $thumb = $container->getThumbnail($args['thumbnail'], false);

                        return stream_get_contents($thumb->getStream());
                    }

                    return null;
                };
            } else {
                //TODO extract duplicate code
                if ($realName == 'fullpath') {
                    $data[$realName] = $container->getThumbnail($thumbnailArgument);
                } elseif ($realName == 'data') {
                    $thumb = $this->getAssetThumbnail($container, $thumbnailArgument, $thumbnailFormat);
                    if ($thumb) {
                        $data[$realName] = stream_get_contents($thumb->getStream());
                    }
                }
            }
        } else {
            if (method_exists($container, $getter)) {
                if ($languageArgument) {
                    if ($ast->alias) {
                        // defer it
                        $data[$realName] = function ($source, $args, $context, ResolveInfo $info) use (
                            $container,
                            $getter
                        ) {
                            return $container->$getter($args['language'] ?? null);
                        };
                    } else {
                        $data[$realName] = $container->$getter($languageArgument);
                    }
                } else {
                    $data[$realName] = $container->$getter();
                }
            }
        }
    }
}
