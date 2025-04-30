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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

/**
 * @internal
 */
final class UrlSlug
{
    use ServiceTrait;

    /**
     * @param \Pimcore\Model\DataObject\Data\UrlSlug|null $value
     * @param array $args
     * @param array $context
     *
     * @return string|null
     *
     * @throws \Exception
     */
    public function resolveSlug($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof \Pimcore\Model\DataObject\Data\UrlSlug) {
            return $value->getSlug();
        }

        return null;
    }

    /**
     * @param \Pimcore\Model\DataObject\Data\UrlSlug|null $value
     * @param array $args
     * @param array $context
     *
     * @return int|null
     *
     * @throws \Exception
     */
    public function resolveSiteId($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof \Pimcore\Model\DataObject\Data\UrlSlug) {
            return $value->getSiteId();
        }

        return null;
    }
}
