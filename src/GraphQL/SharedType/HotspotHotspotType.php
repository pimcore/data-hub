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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\SharedType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

/**
 * Class HotspotHotspotType
 *
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\SharedType
 *
 * @internal
 */
final class HotspotHotspotType extends ObjectType
{
    use ServiceTrait;

    /**
     * @param array $config
     */
    public function __construct(Service $graphQlService, $config = [])
    {
        $this->graphQlService = $graphQlService;
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $propertyType = $this->getGraphQlService()->buildGeneralType('hotspot_metadata');
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\HotspotType();

        $config['fields'] = [
            'top' => Type::float(),
            'left' => Type::float(),
            'height' => Type::float(),
            'width' => Type::float(),
            'data' => [
                'type' => Type::listOf($propertyType),
                'args' => [
                    'keys' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'List of metadata key names to include '
                            . '(if omitted, all entries are returned).',
                    ],
                ],
                'resolve' => [$resolver, 'resolveMetadata'],
            ],
            'name' => Type::string(),
        ];
    }
}
