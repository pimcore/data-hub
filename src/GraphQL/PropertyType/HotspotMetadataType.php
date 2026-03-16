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
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeInterface\Property;
use Pimcore\Model\Element\Data\MarkerHotspotItem;

/**
 * @internal
 */
final class HotspotMetadataType extends UnionType
{
    use ServiceTrait;

    /** @var AssetType */
    protected $assetType;

    /** @var CheckboxType */
    protected $checkboxType;

    /** @var DocumentType */
    protected $documentType;

    /** @var ObjectsType */
    protected $objectType;

    /** @var TextType */
    protected $textareaType;

    /** @var TextType */
    protected $textType;

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

        $this->assetType = $service->getPropertyTypeDefinition('property_asset');
        $this->documentType = $service->getPropertyTypeDefinition('property_document');
        $this->objectType = $service->getPropertyTypeDefinition('property_object');
        $this->checkboxType = $service->getPropertyTypeDefinition('property_checkbox');
        $this->textareaType = $service->getPropertyTypeDefinition('property_textarea');
        $this->textType = $service->getPropertyTypeDefinition('property_text');

        $supportedTypes = [$this->checkboxType, $this->textType, $this->textareaType, $this->assetType, $this->documentType, $this->objectType];

        return $supportedTypes;
    }

    public function resolveType($element, $context, ResolveInfo $info): ObjectType|callable|null
    {
        if ($element instanceof MarkerHotspotItem) {
            $type = $element->getType();
            switch ($type) {
                case 'checkbox': {
                    return $this->checkboxType;
                }
                case 'textarea': {
                    return $this->textareaType;
                }
                case 'textfield': {
                    return $this->textType;
                }
                case 'asset': {
                    return $this->assetType;
                }
                case 'document': {
                    return $this->documentType;
                }
                case 'object': {
                    return $this->objectType; // @phpstan-ignore return.type
                }
                default:
                    throw new ClientSafeException('unkown metadata type: ' . $type);
            }
        }

        return null;
    }
}
