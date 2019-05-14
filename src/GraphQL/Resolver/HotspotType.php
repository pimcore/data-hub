<?php
declare(strict_types=1);
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
use Pimcore\Model\Asset;

/**
 * Class HotspotType
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\Resolver
 */
class HotspotType
{
    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveImage($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $asset = Asset::getById($value['id']);
        $return = [];
        foreach ($asset->getObjectVars() as $fieldName => $var) {
            $return[$fieldName] = $var;
        }

        return !empty($return) ? $return : null;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveCrop($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        return !empty($value['crop']) ? $value['crop'] : null;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveHotspots($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        return !empty($value['hotspots']) ? $value['hotspots'] : null;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveMarker($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        return !empty($value['marker']) ? $value['marker'] : null;
    }
}
