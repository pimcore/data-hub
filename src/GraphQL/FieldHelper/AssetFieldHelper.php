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
    /**
     * @param FieldNode $ast
     * @param array $data
     * @param Asset $container
     * @param $args
     * @param ResolveInfo|null $resolveInfo
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

        $realName = $astName;

        if (($astName == 'fullpath' || $astName == 'data') && $thumbnailArgument && ($container instanceof Image || $container instanceof Video)) {
            if ($ast->alias) {
                // defer it
                $data[$realName] = function ($source, $args, $context, ResolveInfo $info) use ($container, $thumbnailArgument, $realName
                ) {
                    if ($realName == 'fullpath') {
                        return $container->getThumbnail($args['thumbnail'], false);
                    } elseif ($realName == 'data') {
                        $thumb = $container->getThumbnail($args['thumbnail'], false);

                        return stream_get_contents($thumb->getStream());
                    }
                };
            } else {
                //TODO extract duplicate code
                if ($realName == 'fullpath') {
                    $data[$realName] = $container->getThumbnail($thumbnailArgument);
                } elseif ($realName == 'data') {
                    $thumb = $container->getThumbnail($thumbnailArgument, false);
                    $data[$realName] = stream_get_contents($thumb->getStream());
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
