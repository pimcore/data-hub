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

namespace Pimcore\Bundle\DataHubBundle\Controller\Studio\Thumbnails;

use OpenApi\Attributes\Get;
use Pimcore\Bundle\DataHubBundle\OpenApi\Config\Prefix;
use Pimcore\Bundle\DataHubBundle\OpenApi\Config\Tags;
use Pimcore\Bundle\DataHubBundle\Schema\Thumbnail;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ThumbnailServiceInterface;
use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Property\GenericCollection;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\Content\CollectionJson;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\DefaultResponses;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\SuccessResponse;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\HttpResponseCodes;
use Pimcore\Bundle\StudioBackendBundle\Util\Trait\PaginatedResponseTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
final class CollectionController extends AbstractApiController
{
    use PaginatedResponseTrait;

    private const string ROUTE = '/thumbnails';

    public function __construct(
        SerializerInterface $serializer,
        private readonly ThumbnailServiceInterface $thumbnailService,
    ) {
        parent::__construct($serializer);
    }

    #[Route(
        path: self::ROUTE,
        name: 'pimcore_studio_api_data_hub_thumbnails_collection',
        methods: ['GET']
    )]
    #[Get(
        path: Prefix::BUNDLE . self::ROUTE,
        operationId: 'bundle_data_hub_thumbnails_collection',
        description: 'bundle_data_hub_thumbnails_collection_description',
        summary: 'bundle_data_hub_thumbnails_collection_summary',
        tags: [Tags::DataHub->value]
    )]
    #[SuccessResponse(
        description: 'bundle_data_hub_thumbnails_collection_success_response',
        content: new CollectionJson(new GenericCollection(Thumbnail::class)),
    )]
    #[IsGranted('thumbnails')]
    #[DefaultResponses([
        HttpResponseCodes::UNAUTHORIZED,
    ])]
    public function getThumbnails(): JsonResponse
    {
        $thumbnails = $this->thumbnailService->getThumbnails();

        return $this->getPaginatedCollection(
            $this->serializer,
            $thumbnails,
            count($thumbnails)
        );
    }
}

