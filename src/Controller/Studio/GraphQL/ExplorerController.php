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

namespace Pimcore\Bundle\DataHubBundle\Controller\Studio\GraphQL;

use OpenApi\Attributes\Get;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;
use Pimcore\Bundle\DataHubBundle\OpenApi\Attribute\Response\Content\HtmlContent;
use Pimcore\Bundle\DataHubBundle\OpenApi\Config\Prefix;
use Pimcore\Bundle\DataHubBundle\OpenApi\Config\Tags;
use Pimcore\Bundle\DataHubBundle\Service\Studio\GraphQLExplorerServiceInterface;
use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Parameter\Path\IdParameter;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\DefaultResponses;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\HttpResponseCodes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
final class ExplorerController extends AbstractApiController
{
    private const string ROUTE = '/graphql/explorer/{clientname}';

    public function __construct(
        SerializerInterface $serializer,
        private readonly GraphQLExplorerServiceInterface $explorerService
    ) {
        parent::__construct($serializer);
    }

    /**
     * @throws \Exception
     */
    #[Route(
        path: self::ROUTE,
        name: 'pimcore_studio_api_data_hub_graphql_explorer',
        methods: ['GET']
    )]
    #[Get(
        path: Prefix::BUNDLE . self::ROUTE,
        operationId: 'bundle_data_hub_graphql_explorer',
        description: 'bundle_data_hub_graphql_explorer_description',
        summary: 'bundle_data_hub_graphql_explorer_summary',
        tags: [Tags::DataHub->value]
    )]
    #[IdParameter(
        type: 'client',
        schema: new Schema(type: 'string'),
        name: 'clientname',
    )]
    #[Response(
        response: HttpResponseCodes::SUCCESS->value,
        description: 'bundle_data_hub_graphql_explorer_success_response',
        content: new HtmlContent('GraphQL Explorer HTML interface')
    )]
    #[DefaultResponses([
        HttpResponseCodes::UNAUTHORIZED,
        HttpResponseCodes::NOT_FOUND,
    ])]
    public function getExplorer(string $clientname, Request $request): HttpResponse
    {
        $urlParams = array_merge($request->request->all(), $request->query->all());

        return $this->explorerService->generateExplorerResponse($clientname, $urlParams);
    }
}

