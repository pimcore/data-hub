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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\General;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Document;
use Pimcore\Bundle\CoreBundle\DependencyInjection\ContainerAwareInterface;
use Pimcore\Bundle\CoreBundle\DependencyInjection\ContainerAwareTrait;

/**
 * @internal
 */
final class AnyDocumentTargetType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ServiceTrait;

    /**
     * @param array $config
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'AnyDocumentTarget'])
    {
        $this->setGraphQLService($graphQlService);

        parent::__construct($config);
    }

    /**
     *
     * @throws \Exception
     */
    public function getTypes(): array
    {
        $types = [];

        $service = $this->getGraphQlService();
        $documentFolderType = $service->getDocumentTypeDefinition('_document_folder');

        $types[] = $documentFolderType;
        $documentUnionType = $this->getGraphQlService()->getDocumentTypeDefinition('document');
        $supportedDocumentTypes = $documentUnionType->getTypes();
        $types = array_merge($types, $supportedDocumentTypes);

        return $types;
    }

    public function resolveType($element, $context, ResolveInfo $info): ?string
    {
        if ($element) {
            if ($element['__elementType'] == 'document') {
                $document = Document::getById($element['id']);
                if ($document) {
                    $documentType = $document->getType();
                    $service = $this->getGraphQlService();
                    $typeDefinition = $service->getDocumentTypeDefinition('document_' . $documentType);

                    return $typeDefinition;
                }
            } else {
                die('To be done');
            }
        }

        return null;
    }
}
