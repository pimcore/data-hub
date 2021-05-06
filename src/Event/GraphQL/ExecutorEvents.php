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

final class ExecutorEvents
{
    /**
     * @Event("Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\ExecutorEvent")
     *
     * @var string
     */
    const PRE_EXECUTE = 'pimcore.datahub.graphql.executor.preExecute';

    /**
     * @Event("Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\ExecutorResultEvent")
     *
     * @var string
     */
    const POST_EXECUTE = 'pimcore.datahub.graphql.executor.postExecute';
}
