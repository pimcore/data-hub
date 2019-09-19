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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\AssetType\AssetType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

/**
 * Class HotspotType
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType
 */
class HotspotType extends ObjectType
{

    use ServiceTrait;

    /**
     * @var string
     */
    protected $fieldname;


    public function __construct(Service $graphQlService, AssetType $assetType, $config = ['name' => 'hotspotimage'], $context = [])
    {
        $this->setGraphQLService($graphQlService);
        $this->assetType = $assetType;
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\HotspotType();
        $resolver->setGraphQLService($this->getGraphQlService());

        $config['fields'] = [
            'image' => [
                'type' => $this->assetType,
                'resolve' => [$resolver, "resolveImage"],
            ],
            'crop' => [
                'type' => HotspotCropType::getInstance(),
                'resolve' => [$resolver, "resolveCrop"],
            ],
            'hotspots' => [
                'type' => Type::listOf(HotspotHotspotType::getInstance()),
                'resolve' => [$resolver, "resolveHotspots"],
            ],
            'marker' => [
                'type' => Type::listOf(HotspotMarkerType::getInstance()),
                'resolve' => [$resolver, "resolveMarker"],
            ],
        ];
    }

    /**
     * @return string
     */
    public function getFieldname(): string
    {
        return $this->fieldname;
    }

    /**
     * @param string $fieldname
     */
    public function setFieldname(string $fieldname): void
    {
        $this->fieldname = $fieldname;
    }
}
