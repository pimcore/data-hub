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
use Pimcore\Bundle\DataHubBundle\OpenApi\Attribute\Request\FileUploadRequestBody;
use Pimcore\Bundle\DataHubBundle\OpenApi\Config\Prefix;
use Pimcore\Bundle\DataHubBundle\OpenApi\Config\Tags;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Pimcore\Bundle\DataHubBundle\Utils\Constants\PermissionConstants;
use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\CreatedResponse;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\DefaultResponses;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\HttpResponseCodes;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
final class ImportController extends AbstractApiController
{
    private const string ROUTE = '/config/import';

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
        name: 'pimcore_studio_api_data_hub_config_import',
        methods: ['POST']
    )]
    #[Post(
        path: Prefix::BUNDLE . self::ROUTE,
        operationId: 'bundle_data_hub_config_import',
        description: 'bundle_data_hub_config_import_description',
        summary: 'bundle_data_hub_config_import_summary',
        tags: [Tags::DataHub->value]
    )]
    #[FileUploadRequestBody(
        description: 'Configuration file to import',
        propertyDescription: 'JSON configuration file'
    )]
    #[CreatedResponse(
        description: 'bundle_data_hub_config_import_success_response'
    )]
    #[IsGranted(PermissionConstants::PLUGIN_DATA_HUB_CONFIG)]
    #[DefaultResponses([
        HttpResponseCodes::UNAUTHORIZED,
        HttpResponseCodes::BAD_REQUEST,
        HttpResponseCodes::CREATED,
    ])]
    public function importConfiguration(Request $request): Response
    {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('file');

        if (!$file instanceof UploadedFile) {
            return $this->jsonResponse(
                ['error' => 'No file uploaded'],
                HttpResponseCodes::BAD_REQUEST->value
            );
        }

        $json = file_get_contents($file->getPathname());

        if ($json === false) {
            return $this->jsonResponse(
                ['error' => 'Failed to read file contents'],
                HttpResponseCodes::BAD_REQUEST->value
            );
        }

        $result = $this->configurationService->importConfiguration($json);

        return $this->jsonResponse(
            $result,
            HttpResponseCodes::CREATED->value
        );
    }
}
