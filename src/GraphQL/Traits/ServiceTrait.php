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

    public function setGraphQLService(Service $graphQlService)
    {
        $this->graphQlService = $graphQlService;
    }
}
