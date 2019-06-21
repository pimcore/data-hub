<?php

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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class QuantityValueType extends ObjectType
{
    use ServiceTrait;

    protected static $instance;

    /**
     * FolderType constructor.
     * @param Service $graphQlService
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $config = [], $context = [])
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
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QuantityValue();
        $resolver->setGraphQLService($this->getGraphQlService());

        if (!self::$instance) {
            $config =
                [
                    'fields' => [
                        'unit' => [
                            'type' => QuantityValueUnitType::getInstance(),
                            'resolve' => [$resolver, "resolveUnit"]
                        ],
                        'value' => [
                            'type' => Type::float(),
                            'resolve' => [$resolver, "resolveValue"]
                        ],
                        'toString' => [
                            'type' => Type::string(),
                            'resolve' => [$resolver, "resolveToString"],
                            'args' => ['language' => ['type' => Type::string()]]
                        ]

                    ],
                ];
        }
    }
}
