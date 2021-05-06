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

class Geobounds
{
    use ServiceTrait;

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return \Pimcore\Model\DataObject\Data\GeoCoordinates
     *
     * @throws \Exception
     */
    public function resolveNorthEast($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof \Pimcore\Model\DataObject\Data\Geobounds) {
            return $value->getNorthEast();
        }

        return null;
    }

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return \Pimcore\Model\DataObject\Data\GeoCoordinates
     *
     * @throws \Exception
     */
    public function resolveSouthWest($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof \Pimcore\Model\DataObject\Data\Geobounds) {
            return $value->getSouthWest();
        }

        return null;
    }
}
