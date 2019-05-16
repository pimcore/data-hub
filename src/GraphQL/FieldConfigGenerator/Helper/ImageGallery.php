<?php
declare(strict_types=1);
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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldConfigGenerator\Helper;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Data\Hotspotimage;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\Service;

/**
 * Class ImageGallery
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\FieldConfigGenerator\Helper
 */
class ImageGallery
{
    use ServiceTrait;

    /**
     * @var ClassDefinition\Data\ImageGallery
     */
    public $fieldDefinition;

    /**
     * @var ClassDefinition
     */
    public $class;


    /**
     * ImageGallery constructor.
     * @param Service $graphQlService
     * @param ClassDefinition\Data $fieldDefinition
     * @param ClassDefinition $class
     */
    public function __construct(\Pimcore\Bundle\DataHubBundle\GraphQL\Service $graphQlService, ClassDefinition\Data $fieldDefinition, ClassDefinition $class)
    {
        $this->fieldDefinition = $fieldDefinition;
        $this->class = $class;
        $this->setGraphQLService($graphQlService);
    }

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array|null Empty set return null
     *
     * @throws \Exception
     */
    public function resolve($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $result = [];

        $object = AbstractObject::getById($value['id']);
        if ($object instanceof AbstractObject) {
            $getter = 'get' . ucfirst($this->fieldDefinition->getName());
            $relations = $object->$getter();

            if ($relations) {
                /** @var $relation AbstractElement */
                foreach ($relations as $relation) {
                    if ($relation instanceof Hotspotimage) {
                        $image = $relation->getImage();
                    }

                    if ($image instanceof Asset) {
                        $data = new ElementDescriptor();
                        $this->getGraphQlService()->getAssetFieldHelper()->extractData($data, $image, $args, $context, $resolveInfo);
                        $data['data'] = $data['data'] ? base64_encode($data['data']) : null;
                        $data['crop'] = $relation->getCrop();
                        $data['hotspots'] = $relation->getHotspots();
                        $data['marker'] = $relation->getMarker();
                        $data['img'] = $image;
                        $data['image'] = $image->getType();
                        $data['__elementType'] = Service::getType($image);
                        $data['__elementSubtype'] = $image->getType();
                    } else {
                        continue;
                    }

                    $result[] = $data;
                }
            }
        }

        return !empty($result) ? $result : null;
    }
}
