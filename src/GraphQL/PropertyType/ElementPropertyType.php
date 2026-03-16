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

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType\DocumentFolderType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeInterface\Property;
use Pimcore\Model\Asset\Folder;
use Pimcore\Model\Document;

/**
 * @internal
 */
final class ElementPropertyType extends UnionType
{
    use ServiceTrait;

    /** @var AssetType */
    protected $assetType;

    /** @var CheckboxType */
    protected $checkboxType;

    /** @var DocumentType */
    protected $documentType;

    /** @var AssetFolderType */
    protected $assetFolderType;

    /** @var DocumentFolderType */
    protected $documentFolderType;

    /** @var ObjectFolderType */
    protected $objectFolderType;

    /** @var ObjectsType */
    protected $objectType;

    /** @var TextType */
    protected $textType;

    /** @var SelectType */
    protected $selectType;

    /**
     * @param array $config
     */
    public function __construct(Service $graphQlService, $config = [])
    {
        $this->setGraphQLService($graphQlService);
        $config['interfaces'] = [Property::getInstance()];
        parent::__construct($config);
    }

    /**
     *
     * @throws \Exception
     */
    public function getTypes(): array
    {
        $service = $this->getGraphQlService();

        $this->checkboxType = $service->getPropertyTypeDefinition('property_checkbox');
        $this->textType = $service->getPropertyTypeDefinition('property_text');
        $this->selectType = $service->getPropertyTypeDefinition('property_select');

        $supportedTypes = [
            $this->checkboxType,
            $this->textType,
            $this->selectType,
        ];

        if ($this->getGraphQlService()->querySchemaEnabled('asset')) {
            $this->assetType = $service->getPropertyTypeDefinition('property_asset');
            $supportedTypes[] = $this->assetType;
        }

        if ($this->getGraphQlService()->querySchemaEnabled('asset_folder')) {
            $this->assetFolderType = $service->getPropertyTypeDefinition('property_assetfolder');
            $supportedTypes[] = $this->assetFolderType;
        }

        if ($this->getGraphQlService()->querySchemaEnabled('object')) {
            $this->objectType = $service->getPropertyTypeDefinition('property_object');
            $supportedTypes[] = $this->objectType;
        }

        if ($this->getGraphQlService()->querySchemaEnabled('object_folder')) {
            $this->objectFolderType = $service->getPropertyTypeDefinition('property_objectfolder');
            $supportedTypes[] = $this->objectFolderType;
        }

        if ($this->getGraphQlService()->querySchemaEnabled('document')) {
            $this->documentType = $service->getPropertyTypeDefinition('property_document');
            $supportedTypes[] = $this->documentType;
        }

        if ($this->getGraphQlService()->querySchemaEnabled('document_folder')) {
            $this->documentFolderType = $service->getPropertyTypeDefinition('property_documentfolder');
            $supportedTypes[] = $this->documentFolderType;
        }

        return $supportedTypes;
    }

    public function resolveType($element, $context, ResolveInfo $info): ObjectType|callable|null
    {
        if ($element instanceof \Pimcore\Model\Property) {
            $type = $element->getType();
            switch ($type) {
                case 'bool':
                case 'checkbox': {
                    return $this->checkboxType;
                }
                case 'text': {
                    return $this->textType;
                }
                case 'select': {
                    return $this->selectType;
                }
                case 'asset': {
                    $asset = $element->getData();
                    if ($asset instanceof Folder) {
                        return $this->assetFolderType;
                    } else {
                        return $this->assetType;
                    }
                }
                case 'document': {
                    $doc = $element->getData();
                    if ($doc instanceof Document\Folder) {
                        return $this->documentFolderType;
                    } else {
                        return $this->documentType;
                    }
                }
                case 'object': {
                    $object = $element->getData();
                    if ($object instanceof \Pimcore\Model\DataObject\Folder) {
                        return $this->objectFolderType;
                    } else {
                        return $this->objectType; // @phpstan-ignore return.type
                    }
                }
                default:
                    throw new ClientSafeException('unkown property type: ' . $type);
            }
        }

        return null;
    }
}
