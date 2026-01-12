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

namespace Pimcore\Bundle\DataHubBundle\Service\Studio;

use Symfony\Component\HttpFoundation\Response;

/** @internal  */
interface GraphQLExplorerServiceInterface
{
    /**
     * Generate the GraphQL Explorer interface HTML
     *
     * @param string $clientname The GraphQL client/endpoint name
     * @param array<string, mixed> $urlParams Additional URL parameters to pass to the GraphQL endpoint
     * @return Response HTML response with the explorer interface
     * @throws \Exception If the GraphQL endpoint URL cannot be resolved
     */
    public function generateExplorerResponse(string $clientname, array $urlParams = []): Response;
}

