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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper;

use GraphQL\Language\AST\FieldNode;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Asset\Video;

class AssetFieldHelper extends AbstractFieldHelper
{
    public function getVideoThumbnail(Asset\Video $asset, string | Video\Thumbnail\Config $thumbNailConfig, string $thumbNailFormat = null): mixed
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

    public function getImageDocumentThumbnail(Asset $asset, string | Image\Thumbnail\Config $thumbNailConfig, string $thumbNailFormat = null): mixed
    {
        $thumb = null;

        if ($asset instanceof Asset\Document || $asset instanceof Asset\Video) {
            $thumb = $asset->getImageThumbnail($thumbNailConfig);
        } elseif ($asset instanceof Asset\Image) {
            $thumb = $asset->getThumbnail($thumbNailConfig, false);
        }
        if (isset($thumb, $thumbNailFormat) && method_exists($thumb, 'getAsFormat') && !($asset instanceof Asset\Video)) {
            $thumb = $thumb->getAsFormat($thumbNailFormat);
        }

        return $thumb;
    }

    public function getAssetThumbnail(Asset $asset, string | Image\Thumbnail\Config | Video\Thumbnail\Config $thumbNailConfig, string $thumbNailFormat = null): mixed
    {
        if (($asset instanceof Asset\Video) && (is_string($thumbNailConfig) || $thumbNailConfig instanceof Video\Thumbnail\Config)) {
            return $this->getVideoThumbnail($asset, $thumbNailConfig, $thumbNailFormat);
        } else {
            return $this->getImageDocumentThumbnail($asset, $thumbNailConfig, $thumbNailFormat);
        }
    }

    /**
     * @param FieldNode $ast
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
