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
 */
class Hotspotimage
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
     * @param Service $graphQlService
     * @param string $attribute
     * @param ClassDefinition\Data\Hotspotimage $fieldDefinition
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
     * @param ResolveInfo|null $resolveInfo
     *
     * @return ElementDescriptor|null
     *
     * @throws \Exception
     */
    public function resolve($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
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
