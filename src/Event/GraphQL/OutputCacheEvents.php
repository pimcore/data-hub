<?php

declare(strict_types=1);

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
