<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
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
     * AnyTargetType constructor.
     * @param Service $graphQlService
     * @param array $config
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'AnyTarget'])
    {

        $this->setGraphQLService($graphQlService);

        parent::__construct($config);
    }

    /**
     * @return array|\GraphQL\Type\Definition\ObjectType[]
     * @throws \Exception
     */
    public function getTypes()
    {

        $service = $this->getGraphQlService();
        $assetFolderType = $service->getAssetTypeDefinition("_asset_folder");
        $documentFolderType = $service->getDocumentTypeDefinition("_document_folder");
        $objectFolderType = $service->getDataObjectTypeDefinition("_object_folder");


        $types = array_values(ClassTypeDefinitions::getAll());

        $service = $this->getGraphQlService();
        $assetType = $service->buildAssetType("asset");

        $types[] = $assetType;

        $types[] = $assetFolderType;
        $types[] = $documentFolderType;
        $types[] = $objectFolderType;
        $documentUnionType = $this->getGraphQlService()->getDocumentTypeDefinition("document");
        $supportedDocumentTypes = $documentUnionType->getTypes();
        $types = array_merge($types, $supportedDocumentTypes);
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
            } else if ($element['__elementType'] == 'asset') {
                return  $this->getGraphQlService()->getAssetTypeDefinition("asset");
            } else if ($element['__elementType'] == 'document') {
                $document = Document::getById($element['id']);
                if ($document) {
                    $documentType = $document->getType();
                    $service = $this->getGraphQlService();
                    //TODO maybe catch unsupported types for now ?
                    $typeDefinition = $service->getDocumentTypeDefinition("document_" . $documentType);
                    return $typeDefinition;
                }
            }
        }
        return null;
    }
}
