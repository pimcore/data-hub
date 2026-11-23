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

use Pimcore\Bundle\StudioBackendBundle\Security\Authenticator\Mcp\McpAccessTokenAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * The request an MCP client's call arrives in, bound to a chat session or to none.
 */
final class McpRequests
{
    public static function stack(?string $sessionReference): RequestStack
    {
        $request = new Request();
        if ($sessionReference !== null) {
            $request->attributes->set(McpAccessTokenAuthenticator::REQUEST_ATTR_REFERENCE, $sessionReference);
        }

        $requests = new RequestStack();
        $requests->push($request);

        return $requests;
    }
}
