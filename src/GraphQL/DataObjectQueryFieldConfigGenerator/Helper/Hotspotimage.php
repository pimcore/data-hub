<?php
declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Helper;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\BaseDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection;

/**
 * Class Hotspotimage
 *
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Helper
 *
 * @internal
 */
final class Hotspotimage
{
    use ServiceTrait;

    /**
     * @var ClassDefinition\Data\Hotspotimage
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
     * @param string $attribute
     * @param ClassDefinition|Fieldcollection\Definition $class
     */
    public function __construct(
        Service $graphQlService,
        $attribute,
        ClassDefinition\Data\Hotspotimage $fieldDefinition,
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
     *
     * @return ElementDescriptor|null
     *
     * @throws \Exception
     */
    public function resolve($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null)
    {
        $container = Service::resolveValue($value, $this->fieldDefinition, $this->attribute, $args);
        if ($container instanceof \Pimcore\Model\DataObject\Data\Hotspotimage) {
            $image = $container->getImage();
            if ($image instanceof Asset) {
                if (WorkspaceHelper::checkPermission($image, 'read')) {
                    $data = new ElementDescriptor($image);
                    $this->getGraphQlService()->extractData($data, $image, $args, $context, $resolveInfo);

                    $data['crop'] = $container->getCrop();
                    $data['hotspots'] = $container->getHotspots();
                    $data['marker'] = $container->getMarker();

                    return $data;
                }
            }
        }

        return null;
    }
}
