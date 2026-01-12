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

use OpenApi\Attributes\Delete;
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
final class DeleteController extends AbstractApiController
{
    private const string ROUTE = '/config/delete/{name}';

    public function __construct(
        SerializerInterface $serializer,
        private readonly ConfigurationServiceInterface $configurationService
    ) {
        parent::__construct($serializer);
    }

    /**
     * @throws \Exception
     */
    #[Route(
        path: self::ROUTE,
        name: 'pimcore_studio_api_data_hub_config_delete',
        methods: ['DELETE']
    )]
    #[Delete(
        path: Prefix::BUNDLE . self::ROUTE,
        operationId: 'bundle_data_hub_config_delete',
        description: 'bundle_data_hub_config_delete_description',
        summary: 'bundle_data_hub_config_delete_summary',
        tags: [Tags::DataHub->value]
    )]
    #[IdParameter(
        type: 'configuration',
        schema: new Schema(type: 'string'),
        name: 'name',
    )]
    #[SuccessResponse(
        description: 'bundle_data_hub_config_delete_success_response',
    )]
    #[IsGranted(PermissionConstants::PLUGIN_DATA_HUB_CONFIG)]
    #[DefaultResponses([
        HttpResponseCodes::UNAUTHORIZED,
        HttpResponseCodes::NOT_FOUND,
    ])]
    public function deleteConfiguration(string $name): Response
    {
        $this->configurationService->deleteConfiguration($name);

        return new Response();
    }
}

