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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Traits;

use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

trait ServiceTrait
{
    /**
     * @var Service
     */
    protected $graphQlService;

    /**
     * @return Service
     */
    public function getGraphQlService()
    {
        return $this->graphQlService;
    }

    /**
     * @param Service $graphQlService
     */
    public function setGraphQLService(Service $graphQlService)
    {
        $this->graphQlService = $graphQlService;
    }
}
