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

use Pimcore\Bundle\DataHubBundle\Service\CheckConsumerPermissionsService;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\HttpResponseCodes;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @internal
 */
final readonly class GraphQLExplorerService implements GraphQLExplorerServiceInterface
{
    public function __construct(
        private RouterInterface $routingService,
        private Environment $twig
    ) {
    }

    public function generateExplorerResponse(string $clientname, array $urlParams = []): Response
    {
        $graphQLUrl = $this->generateGraphQLUrl($clientname, $urlParams);
        $content = $this->renderExplorerTemplate($graphQLUrl);

        return $this->createCachedResponse($content);
    }

    /**
     * @param array<string, mixed> $urlParams
     * @throws RuntimeException If the GraphQL endpoint URL cannot be resolved
     */
    private function generateGraphQLUrl(string $clientname, array $urlParams): string
    {
        $url = $this->routingService->generate('admin_pimcoredatahub_webservice', [
            'clientname' => $clientname,
        ]);

        if (!$url) {
            throw new RuntimeException('Unable to resolve GraphQL endpoint URL');
        }

        if ($urlParams !== []) {
            $url .= '?' . http_build_query($urlParams);
        }

        return $url;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function renderExplorerTemplate(string $graphQLUrl): string
    {
        return $this->twig->render('@PimcoreDataHub/Feature/explorer.html.twig', [
            'graphQLUrl' => $graphQLUrl,
            'tokenHeader' => CheckConsumerPermissionsService::TOKEN_HEADER,
        ]);
    }

    private function createCachedResponse(string $content): Response
    {
        $response = new Response($content, HttpResponseCodes::SUCCESS->value, [
            'Content-Type' => 'text/html',
        ]);

        $response->setPublic();
        $response->setExpires(new \DateTime('tomorrow'));

        return $response;
    }
}

