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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class QuantityValueType extends ObjectType
{
    use ServiceTrait;

    /** @var Data */
    protected $fieldDefinition;

    /**
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, ?Data $fieldDefinition = null, $config = [], $context = [])
    {
        $this->fieldDefinition = $fieldDefinition;
        $this->setGraphQLService($graphQlService);
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $valueType = Type::float();
        if (isset($config['fields']['value']['type'])) {
            $valueType = $config['fields']['value']['type'];
        }

        $config['fields'] = self::getFieldConfig($this->getGraphQlService(), $valueType);
    }

    /**
     * @param string $valueType
     *
     * @return array[]
     */
    public static function getFieldConfig(Service $graphQlService, $valueType)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QuantityValue();
        $resolver->setGraphQLService($graphQlService);
        $fields = [
            'unit' => [
                'type' => QuantityValueUnitType::getInstance(),
                'resolve' => [$resolver, 'resolveUnit'],
            ],
            'value' => [
                'type' => $valueType,
                'resolve' => [$resolver, 'resolveValue'],
            ],
            'toString' => [
                'type' => Type::string(),
                'resolve' => [$resolver, 'resolveToString'],
                'args' => ['language' => ['type' => Type::string()]],
            ],
        ];

        return $fields;
    }
}
