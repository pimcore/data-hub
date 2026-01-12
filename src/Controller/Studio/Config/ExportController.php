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

namespace Pimcore\Bundle\DataHubBundle\Controller\Studio\Config;

use Exception;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Schema;
use Pimcore\Bundle\DataHubBundle\OpenApi\Config\Prefix;
use Pimcore\Bundle\DataHubBundle\OpenApi\Config\Tags;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Pimcore\Bundle\DataHubBundle\Utils\Constants\PermissionConstants;
use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Parameter\Path\IdParameter;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\DefaultResponses;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\SuccessResponse;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\HttpResponseCodes;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
final class ExportController extends AbstractApiController
{
    private const string ROUTE = '/config/{name}/export';

    public function __construct(
        SerializerInterface $serializer,
        private readonly ConfigurationServiceInterface $configurationService
    ) {
        parent::__construct($serializer);
    }

    /**
     * @throws Exception
     */
    #[Route(
        path: self::ROUTE,
        name: 'pimcore_studio_api_data_hub_config_export',
        methods: ['GET']
    )]
    #[Get(
        path: Prefix::BUNDLE . self::ROUTE,
        operationId: 'bundle_data_hub_config_export',
        description: 'bundle_data_hub_config_export_description',
        summary: 'bundle_data_hub_config_export_summary',
        tags: [Tags::DataHub->value]
    )]
    #[IdParameter(
        type: 'configuration',
        schema: new Schema(type: 'string'),
        name: 'name',
    )]
    #[SuccessResponse(
        description: 'bundle_data_hub_config_export_success_response',
    )]
    #[IsGranted(PermissionConstants::PLUGIN_DATA_HUB_CONFIG)]
    #[DefaultResponses([
        HttpResponseCodes::UNAUTHORIZED,
        HttpResponseCodes::NOT_FOUND,
    ])]
    public function exportConfiguration(string $name): Response
    {
        $result = $this->configurationService->exportConfiguration($name);

        $response = new Response($result['json']);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set(
            'Content-Disposition',
            sprintf('attachment; filename="%s"', $result['filename'])
        );

        return $response;
    }
}
