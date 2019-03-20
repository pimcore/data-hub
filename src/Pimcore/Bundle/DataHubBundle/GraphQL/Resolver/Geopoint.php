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


class Geopoint
{

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveText($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof \Pimcore\Model\DataObject\Data\Link) {
            return $value->getText();
        }

        return null;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolvePath($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof \Pimcore\Model\DataObject\Data\Link) {
            return $value->getPath();
        }

        return null;
    }

}

