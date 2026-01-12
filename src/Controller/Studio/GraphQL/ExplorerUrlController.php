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
use OpenApi\Attributes\Schema;
use Pimcore\Bundle\DataHubBundle\OpenApi\Attribute\Response\Content\ExplorerUrlJson;
use Pimcore\Bundle\DataHubBundle\OpenApi\Config\Prefix;
use Pimcore\Bundle\DataHubBundle\OpenApi\Config\Tags;
use Pimcore\Bundle\DataHubBundle\Service\Studio\GraphQLExplorerServiceInterface;
use Pimcore\Bundle\DataHubBundle\Utils\Constants\PermissionConstants;
use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Parameter\Path\IdParameter;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\DefaultResponses;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\SuccessResponse;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\HttpResponseCodes;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
final class ExplorerUrlController extends AbstractApiController
{
    private const string ROUTE = '/graphql/explorer-url/{name}';

    public function __construct(
        SerializerInterface $serializer,
        private readonly GraphQLExplorerServiceInterface $graphQLExplorerService
    ) {
        parent::__construct($serializer);
    }

    /**
     * @throws \Exception
     */
    #[Route(
        path: self::ROUTE,
        name: 'pimcore_studio_api_data_hub_graphql_explorer_url',
        methods: ['GET']
    )]
    #[Get(
        path: Prefix::BUNDLE . self::ROUTE,
        operationId: 'bundle_data_hub_graphql_explorer_url',
        description: 'bundle_data_hub_graphql_explorer_url_description',
        summary: 'bundle_data_hub_graphql_explorer_url_summary',
        tags: [Tags::DataHub->value]
    )]
    #[IdParameter(
        type: 'configuration',
        schema: new Schema(type: 'string'),
        name: 'name',
    )]
    #[SuccessResponse(
        description: 'bundle_data_hub_graphql_explorer_url_success_response',
        content: new ExplorerUrlJson()
    )]
    #[IsGranted(PermissionConstants::PLUGIN_DATA_HUB_CONFIG)]
    #[DefaultResponses([
        HttpResponseCodes::UNAUTHORIZED,
        HttpResponseCodes::NOT_FOUND,
    ])]
    public function getExplorerUrl(string $name): JsonResponse
    {
        return $this->jsonResponse(
            [
                'explorerUrl' => $this->graphQLExplorerService->getExplorerUrl($name),
            ]
        );
    }
}

