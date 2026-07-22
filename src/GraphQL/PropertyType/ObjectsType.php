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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\PropertyType;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\CoreBundle\DependencyInjection\ContainerAwareInterface;
use Pimcore\Bundle\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Document;

/**
 * @internal
 */
final class ObjectsType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ServiceTrait;

    public function __construct(Service $graphQlService)
    {
        $this->setGraphQLService($graphQlService);

        // @phpstan-ignore-next-line - We can't define the types in the constructor because the `getTypes` method is overwritten
        parent::__construct(['name' => 'hotspot_metadata_object']);
    }

    /**
     *
     * @throws \Exception
     */
    public function getTypes(): array
    {
        $types = [];

        $service = $this->getGraphQlService();

        if ($service->querySchemaEnabled('object')) {
            $objectTypes = array_values(ClassTypeDefinitions::getAll(true));
            $types = array_merge($types, $objectTypes);
        }

        if ($service->querySchemaEnabled('object_folder')) {
            $types[] = $this->getGraphQlService()->getDataObjectTypeDefinition('_object_folder');
        }

        if ($service->querySchemaEnabled('document')) {
            $documentUnionType = $this->getGraphQlService()->getDocumentTypeDefinition('document');
            $supportedDocumentTypes = $documentUnionType->getTypes();
            $types = array_merge($types, $supportedDocumentTypes);
        }

        if ($service->querySchemaEnabled('asset')) {
            $types[] = $this->getGraphQlService()->buildAssetType('asset');
        }

        if ($service->querySchemaEnabled('asset_folder')) {
            $types[] = $this->getGraphQlService()->getAssetTypeDefinition('_asset_folder');
        }

        return $types;
    }

    public function resolveType($element, $context, ResolveInfo $info): ObjectType|string|callable|Deferred|null
    {
        if ($element) {
            if ($element['__elementType'] == 'object') {
                if ($element['__elementSubtype'] === 'folder') {
                    return $this->getGraphQlService()->getDataObjectTypeDefinition('_object_folder');
                }

                return ClassTypeDefinitions::get($element['__elementSubtype']);
            } elseif ($element['__elementType'] == 'asset') {
                return  $this->getGraphQlService()->buildAssetType('asset');
            } elseif ($element['__elementType'] == 'document') {
                $document = Document::getById($element['id']);
                if ($document) {
                    $documentType = $document->getType();
                    $service = $this->getGraphQlService();
                    $typeDefinition = $service->getDocumentTypeDefinition('document_' . $documentType);

                    return $typeDefinition;
                }
            }
        }

        return null;
    }
}
