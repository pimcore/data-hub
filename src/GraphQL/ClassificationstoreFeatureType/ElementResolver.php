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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\ElementMetadata;
use Pimcore\Model\DataObject\Data\Hotspotimage;
use Pimcore\Model\DataObject\Data\ObjectMetadata;
use Pimcore\Model\Document;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service as ElementService;

/**
 * Resolves the elements a Classification Store key points at, the way the plain field helpers do.
 *
 * @internal
 */
final class ElementResolver
{
    use ServiceTrait;

    /**
     * @param bool $objectsOnly the key points at data objects only (manyToManyObjectRelation): anything that is not a
     *                          concrete object, an object folder for instance, has no member in the target union
     */
    public function __construct(Service $graphQlService, private readonly bool $objectsOnly = false)
    {
        $this->setGraphQLService($graphQlService);
    }

    public function resolveElement($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null)
    {
        if (!$value instanceof FeatureDescriptor) {
            return null;
        }

        return $this->describe($value->getValue(), $args, $context, $resolveInfo);
    }

    public function resolveElements($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null)
    {
        if (!$value instanceof FeatureDescriptor || !is_array($value->getValue()) || !$value->getValue()) {
            return null;
        }

        $result = [];
        foreach ($value->getValue() as $element) {
            $data = $this->describe($element, $args, $context, $resolveInfo);
            if ($data !== null) {
                $result[] = $data;
            }
        }

        return $result;
    }

    public function resolveAdvancedElements($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null)
    {
        if (!$value instanceof FeatureDescriptor || !is_array($value->getValue()) || !$value->getValue()) {
            return null;
        }

        $result = [];
        foreach ($value->getValue() as $relation) {
            if (!$relation instanceof ObjectMetadata && !$relation instanceof ElementMetadata) {
                continue;
            }

            $data = $this->describe($relation->getElement(), $args, $context, $resolveInfo);
            if ($data === null) {
                continue;
            }

            $metadata = [];
            foreach ((array)$relation->getData() as $name => $metaValue) {
                $metadata[] = ['name' => $name, 'value' => $metaValue];
            }

            $result[] = ['element' => $data, 'metadata' => $metadata];
        }

        return $result;
    }

    public function resolveHotspotimage($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null)
    {
        if (!$value instanceof FeatureDescriptor) {
            return null;
        }

        return $this->describeHotspotimage($value->getValue(), $args, $context, $resolveInfo);
    }

    public function resolveImageGallery($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null)
    {
        if (!$value instanceof FeatureDescriptor || !is_iterable($value->getValue())) {
            return null;
        }

        $result = [];
        foreach ($value->getValue() as $hotspotimage) {
            $data = $this->describeHotspotimage($hotspotimage, $args, $context, $resolveInfo);
            if ($data !== null) {
                $image = $hotspotimage->getImage();
                $data['data'] = isset($data['data']) ? base64_encode($data['data']) : null;
                $data['img'] = $image;
                $data['image'] = $image->getType();
                $data['__elementType'] = ElementService::getElementType($image);
                $data['__elementSubtype'] = $image->getType();
                $result[] = $data;
            }
        }

        return !empty($result) ? $result : null;
    }

    private function describe($element, $args, $context, ?ResolveInfo $resolveInfo): ?ElementDescriptor
    {
        if (!$element instanceof ElementInterface || !$this->isPublishable($element)
            || !WorkspaceHelper::checkPermission($element, 'read')) {
            return null;
        }

        $data = new ElementDescriptor($element);
        $this->getGraphQlService()->extractData($data, $element, $args, $context, $resolveInfo);

        return $data;
    }

    /**
     * An element can only be resolved when the union it is returned through has a member for it: folders follow their
     * own read setting (WorkspaceHelper), every other type needs to be enabled for query in the endpoint.
     */
    private function isPublishable(ElementInterface $element): bool
    {
        if ($this->objectsOnly && !$element instanceof Concrete) {
            return false;
        }

        if ($element instanceof Concrete) {
            return $this->getGraphQlService()->querySchemaEnabled('object');
        }
        if ($element instanceof Asset) {
            return $element instanceof Asset\Folder || $this->getGraphQlService()->querySchemaEnabled('asset');
        }
        if ($element instanceof Document) {
            return $element instanceof Document\Folder || $this->getGraphQlService()->querySchemaEnabled('document');
        }

        return true;
    }

    private function describeHotspotimage($hotspotimage, $args, $context, ?ResolveInfo $resolveInfo): ?ElementDescriptor
    {
        if (!$hotspotimage instanceof Hotspotimage || !$hotspotimage->getImage() instanceof Asset) {
            return null;
        }

        $data = $this->describe($hotspotimage->getImage(), $args, $context, $resolveInfo);
        if ($data === null) {
            return null;
        }

        $data['crop'] = $hotspotimage->getCrop();
        $data['hotspots'] = $hotspotimage->getHotspots();
        $data['marker'] = $hotspotimage->getMarker();

        return $data;
    }
}
