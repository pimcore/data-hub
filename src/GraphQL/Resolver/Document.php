<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Document\Service as DocumentService;

/**
 * @internal
 */
final class Document extends Element
{
    use ServiceTrait;

    /**
     * @var DocumentService
     */
    protected $documentService;

    public function __construct(DocumentService $documentService, Service $graphQlService)
    {
        parent::__construct('document', $graphQlService);

        $this->documentService = $documentService;
        $this->setGraphQLService($graphQlService);
    }

    /**
     * @param array $value
     * @param array $args
     * @param array $context
     *
     * @throws \Exception
     */
    public function resolveTranslations($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null): array
    {
        $languageRequested = $args['defaultLanguage'] ?? null;

        $document = \Pimcore\Model\Document::getById($value['id']);
        $result = [];

        if ($document) {
            $documentId = $document->getId();
            foreach ($this->documentService->getTranslations($document) as $transLanguage => $transId) {
                if ($transId === $documentId || ($languageRequested && $languageRequested !== $transLanguage)) {
                    continue;
                }

                $result[] = [
                    'id' => $transId,
                    'language' => $transLanguage,
                    'target' => $this->resolveTranslationTarget($value, $args, $context, $resolveInfo),
                ];
            }
        }

        return $result;
    }

    /**
     * @param array $value
     * @param array $args
     * @param array $context
     *
     * @throws \Exception
     */
    public function resolveTranslationTarget($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null): ?ElementDescriptor
    {
        $document = \Pimcore\Model\Document::getById($value['id']);
        if ($document instanceof \Pimcore\Model\Document) {
            return $this->extractSingleElement($document, $args, $context, $resolveInfo);
        }

        return null;
    }
}
