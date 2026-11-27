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

use Pimcore\Bundle\PimcoreAgentBundle\Mcp\McpProposalContext;
use Pimcore\Bundle\PimcoreAgentBundle\Repository\AgentSessionRepository;
use Pimcore\Bundle\PimcoreAgentBundle\Security\BoundSessionReferenceResolver;
use Pimcore\Bundle\StudioBackendBundle\Security\Authenticator\Mcp\McpAccessTokenAuthenticator;
use Pimcore\Bundle\StudioBackendBundle\Security\Service\SecurityServiceInterface;
use Pimcore\Model\Version\CoauthorContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * The request an MCP client's call arrives in, bound to a chat session or to none.
 */
final class McpRequests
{
    private const string SECRET = 'test-secret';

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

    /**
     * An external client's call to a Studio MCP server: no chat session is bound.
     */
    public static function serverResolver(
        string $serverSlug,
        SecurityServiceInterface $security,
        AgentSessionRepository $sessions,
    ): BoundSessionReferenceResolver {
        $requests = self::stack(null);
        $request = $requests->getCurrentRequest();
        $request->attributes->set('_route', McpProposalContext::SERVER_ROUTE);
        $request->attributes->set(McpProposalContext::SERVER_ROUTE_SLUG, $serverSlug);

        return new BoundSessionReferenceResolver(
            $requests,
            new McpProposalContext($requests, new CoauthorContext(), $security, $sessions, self::SECRET),
        );
    }

    /**
     * The session a proposal of that user on that server is stored in.
     */
    public static function serverSessionId(int $userId, string $serverSlug): string
    {
        return McpProposalContext::sessionIdOf(self::SECRET, $userId, $serverSlug);
    }
}
