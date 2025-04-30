<?php

declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Event\GraphQL;

final class OutputCacheEvents
{
    /**
     * Fired to determine if a response should be cached.
     *
     * @Event("Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\CachePreLoadEvent")
     *
     * @var string
     */
    const PRE_LOAD = 'pimcore.datahub.graphql.cache.preLoad';

    /**
     * Fired before the response is written to cache. Can be used to set or purge
     * data on the cached response.
     *
     * @Event("Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\CachePreSaveEvent")
     *
     * @var string
     */
    const PRE_SAVE = 'pimcore.datahub.graphql.cache.preSave';
}
