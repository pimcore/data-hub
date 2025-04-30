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
