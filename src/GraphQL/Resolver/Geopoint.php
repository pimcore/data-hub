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
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\Data\GeoCoordinates;

class Geopoint
{
    use ServiceTrait;

    /**
     * @param mixed $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return float|null
     *
     * @throws \Exception
     */
    public function resolveLongitude($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof GeoCoordinates) {
            return $value->getLongitude();
        }

        return null;
    }

    /**
     * @param mixed $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return float|null
     *
     * @throws \Exception
     */
    public function resolveLatitude($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof GeoCoordinates) {
            return $value->getLatitude();
        }

        return null;
    }
}
