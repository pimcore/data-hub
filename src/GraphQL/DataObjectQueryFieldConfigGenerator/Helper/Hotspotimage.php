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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Helper;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Asset;
use Pimcore\Model\Element\Service;


/**
 * Class Hotspotimage
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Helper
 */
class Hotspotimage
{
    use ServiceTrait;

    /**
     * @var
     */
    public $fieldDefinition;

    /**
     * @var
     */
    public $class;

    /**
     * @var
     */
    public $attribute;


    /**
     * ImageGallery constructor.
     * @param Service $graphQlService
     * @param $fieldDefinition
     * @param $class
     */
    public function __construct(\Pimcore\Bundle\DataHubBundle\GraphQL\Service $graphQlService, $attribute, $fieldDefinition,  $class)
    {
        $this->fieldDefinition = $fieldDefinition;
        $this->class = $class;
        $this->attribute = $attribute;
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
        /** @var  $container Hotspotimage */
        $container = \Pimcore\Bundle\DataHubBundle\GraphQL\Service::resolveValue($value, $this->fieldDefinition, $this->attribute, $args);
        if ($container instanceof \Pimcore\Model\DataObject\Data\Hotspotimage) {
            $image = $container->getImage();
            if ($image instanceof Asset) {
                if (!WorkspaceHelper::isAllowed($image, $context['configuration'], 'read')) {
                    throw new \Exception('permission denied. check your workspace settings');
                }

                $data = new ElementDescriptor($image);
                $this->getGraphQlService()->extractData($data, $image, $args, $context, $resolveInfo);

                $data['crop'] = $container->getCrop();
                $data['hotspots'] = $container->getHotspots();
                $data['marker'] = $container->getMarker();
                return $data;
            }
        }

        return !empty($result) ? $result : null;
    }
}
