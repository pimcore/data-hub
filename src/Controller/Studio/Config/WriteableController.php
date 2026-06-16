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

use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use Pimcore\Bundle\DataHubBundle\OpenApi\Config\Prefix;
use Pimcore\Bundle\DataHubBundle\OpenApi\Config\Tags;
use Pimcore\Bundle\DataHubBundle\Schema\ConfigurationsWriteable;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Pimcore\Bundle\DataHubBundle\Utils\Constants\PermissionConstants;
use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\DefaultResponses;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\SuccessResponse;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\HttpResponseCodes;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
final class WriteableController extends AbstractApiController
{
    private const string ROUTE = '/config/writeable';

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
        name: 'pimcore_studio_api_data_hub_config_writeable',
        methods: ['GET'],
        priority: 10
    )]
    #[Get(
        path: Prefix::BUNDLE . self::ROUTE,
        operationId: 'bundle_data_hub_config_writeable',
        description: 'bundle_data_hub_config_writeable_description',
        summary: 'bundle_data_hub_config_writeable_summary',
        tags: [Tags::DataHub->value]
    )]
    #[SuccessResponse(
        description: 'bundle_data_hub_config_writeable_success_response',
        content: new JsonContent(ref: ConfigurationsWriteable::class)
    )]
    #[IsGranted(PermissionConstants::PLUGIN_DATA_HUB_CONFIG)]
    #[DefaultResponses([
        HttpResponseCodes::UNAUTHORIZED,
    ])]
    public function getWriteable(): JsonResponse
    {
        return $this->jsonResponse(
            new ConfigurationsWriteable($this->configurationService->areConfigurationsWriteable())
        );
    }
}
