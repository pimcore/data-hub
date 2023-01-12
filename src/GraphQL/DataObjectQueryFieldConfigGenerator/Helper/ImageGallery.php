<?php
declare(strict_types=1);

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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Helper;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\BaseDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service as GraphQlService;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Data\Hotspotimage;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\Element\Service;

/**
 * Class ImageGallery
 *
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Helper
 */
class ImageGallery
{
    use ServiceTrait;

    /**
     * @var ClassDefinition\Data\ImageGallery
     */
    public $fieldDefinition;

    /**
     * @var ClassDefinition|Fieldcollection\Definition
     */
    public $class;

    /**
     * @var string
     */
    public $attribute;

    /**
     * @param GraphQlService $graphQlService
     * @param string $attribute
     * @param ClassDefinition\Data\ImageGallery $fieldDefinition
     * @param ClassDefinition|Fieldcollection\Definition $class
     */
    public function __construct(
        GraphQlService $graphQlService,
        $attribute,
        ClassDefinition\Data\ImageGallery $fieldDefinition,
        $class
    ) {
        $this->fieldDefinition = $fieldDefinition;
        $this->class = $class;
        $this->attribute = $attribute;
        $this->setGraphQLService($graphQlService);
    }

    /**
     * @param BaseDescriptor|null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return ElementDescriptor[]|null
     *
     * @throws \Exception
     */
    public function resolve($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $result = [];
        $relations = GraphQlService::resolveValue($value, $this->fieldDefinition, $this->attribute, $args);
        if ($relations) {
            foreach ($relations as $relation) {
                if ($relation instanceof Hotspotimage) {
                    $image = $relation->getImage();
                } else {
                    continue;
                }

                if ($image instanceof Asset) {
                    if (!WorkspaceHelper::checkPermission($image, 'read')) {
                        continue;
                    }

                    $data = new ElementDescriptor($image);
                    $this->getGraphQlService()->extractData($data, $image, $args, $context, $resolveInfo);

                    $data['data'] = isset($data['data']) ? base64_encode($data['data']) : null;
                    $data['crop'] = $relation->getCrop();
                    $data['hotspots'] = $relation->getHotspots();
                    $data['marker'] = $relation->getMarker();
                    $data['img'] = $image;
                    $data['image'] = $image->getType();
                    $data['__elementType'] = Service::getElementType($image);
                    $data['__elementSubtype'] = $image->getType();
                } else {
                    continue;
                }

                $result[] = $data;
            }
        }

        return !empty($result) ? $result : null;
    }
}
