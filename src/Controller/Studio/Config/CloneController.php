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

use OpenApi\Attributes\Post;
use Pimcore\Bundle\DataHubBundle\OpenApi\Config\Prefix;
use Pimcore\Bundle\DataHubBundle\OpenApi\Config\Tags;
use Pimcore\Bundle\DataHubBundle\Schema\CloneConfiguration;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Parameter\Query\StringParameter;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\CreatedResponse;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\DefaultResponses;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\HttpResponseCodes;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
final class CloneController extends AbstractApiController
{
    private const string ROUTE = '/config/clone';

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
        name: 'pimcore_studio_api_data_hub_config_clone',
        methods: ['POST']
    )]
    #[Post(
        path: Prefix::BUNDLE . self::ROUTE,
        operationId: 'bundle_data_hub_config_clone',
        description: 'bundle_data_hub_config_clone_description',
        summary: 'bundle_data_hub_config_clone_summary',
        tags: [Tags::DataHub->value]
    )]
    #[StringParameter('name', 'assets_copy', 'The name of the new configuration')]
    #[StringParameter('originalName', 'assets', 'The name of the configuration to clone')]
    #[CreatedResponse(
        description: 'bundle_data_hub_config_clone_success_response'
    )]
    #[DefaultResponses([
        HttpResponseCodes::UNAUTHORIZED,
        HttpResponseCodes::NOT_FOUND,
        HttpResponseCodes::CREATED,
    ])]
    public function cloneConfiguration(
        #[MapQueryString] CloneConfiguration $cloneConfiguration
    ): Response {
        $clonedConfigName = $this->configurationService->cloneConfiguration(
            $cloneConfiguration->getName(),
            $cloneConfiguration->getOriginalName()
        );

        return $this->jsonResponse(
            [
                'clonedConfigurationName' => $clonedConfigName,
            ],
            HttpResponseCodes::CREATED->value
        );
    }
}
