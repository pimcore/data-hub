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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\SharedType\HotspotCropType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

/**
 * Class HotspotType
 *
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType
 */
class HotspotType extends ObjectType
{
    use ServiceTrait;

    /**
     * @param Service $graphQlService
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'hotspotimage'], $context = [])
    {
        $this->setGraphQLService($graphQlService);
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
        $service = $this->getGraphQlService();
        $assetType = $service->buildAssetType('asset');
        $hotspotMarkerType = $service->buildGeneralType('hotspotmarker');
        $hotspotHotspotType = $service->buildGeneralType('hotspothotspot');

        $config['fields'] = [
            'image' => [
                'type' => $assetType,
                'resolve' => [$resolver, 'resolveImage'],
            ],
            'crop' => [
                'type' => HotspotCropType::getInstance(),
                'resolve' => [$resolver, 'resolveCrop'],
            ],
            'hotspots' => [
                'type' => Type::listOf($hotspotHotspotType),
                'resolve' => [$resolver, 'resolveHotspots'],
            ],
            'marker' => [
                'type' => Type::listOf($hotspotMarkerType),
                'resolve' => [$resolver, 'resolveMarker'],
            ],
        ];
    }
}
