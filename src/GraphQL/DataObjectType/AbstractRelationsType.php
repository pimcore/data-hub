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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\CoreBundle\DependencyInjection\ContainerAwareInterface;
use Pimcore\Bundle\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType\DocumentType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Fieldcollection\Definition;
use Pimcore\Model\Document;

abstract class AbstractRelationsType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ServiceTrait;

    /** @var ClassDefinition */
    protected $class;

    /** @var Data */
    protected $fieldDefinition;

    /**
     * @param ClassDefinition|Definition|null $class
     * @param array $config
     */
    public function __construct(Service $graphQlService, ?Data $fieldDefinition = null, $class = null, $config = [])
    {
        $this->class = $class;
        $this->fieldDefinition = $fieldDefinition;
        $this->setGraphQLService($graphQlService);
        $name = null;

        if ($fieldDefinition && $class) {
            if ($class instanceof ClassDefinition) {
                $name = 'object_' . $class->getName() . '_' . $fieldDefinition->getName();
            } elseif ($class instanceof Definition) {
                $name = 'fieldcollection_' . $class->getKey() . '_' . $fieldDefinition->getName();
            }
        }
        if ($fieldDefinition instanceof Data\AdvancedManyToManyRelation || $fieldDefinition instanceof Data\AdvancedManyToManyObjectRelation) {
            $name .= '_element';
        }

        $config['name'] = $name;
        parent::__construct($config);
    }

    /**
     * @return ClassDefinition|Definition|null
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class): void
    {
        $this->class = $class;
    }

    /**
     *
     * @throws \Exception
     */
    public function getTypes(): array
    {
        $fd = $this->getFieldDefinition();

        $types = [];

        if ($fd->getObjectsAllowed()) {
            if (!$fd->getClasses()) {
                $types = array_merge($types, array_values(ClassTypeDefinitions::getAll()));
                // without a class restriction folders are valid relation targets as well,
                // but they are no class definition and therefore not part of getAll()
                $types[] = $this->getGraphQlService()->getDataObjectTypeDefinition('_object_folder');
            } else {
                $classes = $fd->getClasses();
                if (!is_array($classes)) {
                    $classes = [$classes];
                }
                foreach ($classes as $className) {
                    if (is_array($className)) {
                        $className = $className['classes'];
                    }
                    if ($className === 'folder') {
                        $types[] = $this->getGraphQlService()->getDataObjectTypeDefinition('_object_folder');

                        continue;
                    }
                    $types[] = ClassTypeDefinitions::get($className);
                }
            }
        }

        if (!$fd instanceof Data\ManyToManyObjectRelation) {
            if ($fd->getAssetsAllowed()) {
                $service = $this->getGraphQlService();
                $assetType = $service->buildAssetType('asset');

                $types[] = $assetType;
            }

            if ($fd->getDocumentsAllowed()) {
                /** @var DocumentType $documentUnionType */
                $documentUnionType = $this->getGraphQlService()->getDocumentTypeDefinition('document');
                $supportedDocumentTypes = $documentUnionType->getTypes();
                $types = array_merge($types, $supportedDocumentTypes);
            }
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
                    if ($documentType === 'folder') {
                        return $service->getDocumentTypeDefinition('_document_folder');
                    }

                    //TODO maybe catch unsupported types for now ?
                    $typeDefinition = $service->getDocumentTypeDefinition('document_' . $documentType);

                    return $typeDefinition;
                }
            }
        }

        return null;
    }

    public function getFieldDefinition(): Data
    {
        return $this->fieldDefinition;
    }
}
