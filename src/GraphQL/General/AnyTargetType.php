<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\General;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Document;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class AnyTargetType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    use ServiceTrait;

    /**
     * @param Service $graphQlService
     * @param array $config
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'AnyTarget'])
    {
        $this->setGraphQLService($graphQlService);

        parent::__construct($config);
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getTypes(): array
    {
        $service = $this->getGraphQlService();

        $types = [];

        if ($service->querySchemaEnabled('object')) {
            $objectTypes = array_values(ClassTypeDefinitions::getAll(false));
            $types = $objectTypes;
        }

        if ($service->querySchemaEnabled('asset')) {
            $assetType = $service->buildAssetType('asset');
            $types[] = $assetType;
        }

        if ($service->querySchemaEnabled('asset_folder')) {
            $assetFolderType = $service->getAssetTypeDefinition('_asset_folder');
            $types[] = $assetFolderType;
        }

        if ($service->querySchemaEnabled('document_folder')) {
            $documentFolderType = $service->getDocumentTypeDefinition('_document_folder');
            $types[] = $documentFolderType;
        }

        if ($service->querySchemaEnabled('object_folder')) {
            $objectFolderType = $service->getDataObjectTypeDefinition('_object_folder');
            $types[] = $objectFolderType;
        }

        if ($service->querySchemaEnabled('document')) {
            $documentUnionType = $service->getDocumentTypeDefinition('document');
            $supportedDocumentTypes = $documentUnionType->getTypes();
            $types = array_merge($types, $supportedDocumentTypes);
        }

        return $types;
    }

    /**
     * @inheritdoc
     */
    public function resolveType($element, $context, ResolveInfo $info)
    {
        if ($element) {
            if ($element['__elementType'] == 'object') {
                $type = ClassTypeDefinitions::get($element['__elementSubtype']);

                return $type;
            } elseif ($element['__elementType'] == 'asset') {
                return  $this->getGraphQlService()->buildAssetType('asset');
            } elseif ($element['__elementType'] == 'document') {
                $document = Document::getById($element['id']);
                if ($document) {
                    $documentType = $document->getType();
                    $service = $this->getGraphQlService();
                    //TODO maybe catch unsupported types for now ?
                    $typeDefinition = $service->getDocumentTypeDefinition('document_' . $documentType);

                    return $typeDefinition;
                }
            }
        }

        return null;
    }
}
