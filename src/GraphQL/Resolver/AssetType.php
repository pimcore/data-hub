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
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Asset;


class AssetType
{

    use ServiceTrait;

    /**
     * AssetType constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
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

}

