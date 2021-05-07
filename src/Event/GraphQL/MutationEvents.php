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

namespace Pimcore\Bundle\DataHubBundle\Event\GraphQL;

final class MutationEvents
{
    /**
     * @Event("Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\MutationTypeEvent")
     *
     * @var string
     */
    const PRE_BUILD = 'pimcore.datahub.graphql.mutation.preBuild';

    /**
     * @Event("Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\MutationTypeEvent")
     *
     * @var string
     */
    const POST_BUILD = 'pimcore.datahub.graphql.mutation.postBuild';
}
