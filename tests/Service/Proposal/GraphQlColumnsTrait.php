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

namespace Pimcore\Bundle\DataHubBundle\Tests\Service\Proposal;

use Pimcore;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\GraphQlColumns;

/**
 * The column check on the kernel's own GraphQL service, so it knows the generators the schema builder uses.
 */
trait GraphQlColumnsTrait
{
    private function graphQlColumns(): GraphQlColumns
    {
        $graphQl = Pimcore::getContainer()->get(Service::class);
        $this->assertInstanceOf(Service::class, $graphQl);

        return new GraphQlColumns($graphQl);
    }
}
