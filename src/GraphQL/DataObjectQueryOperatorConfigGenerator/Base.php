<?php

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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryOperatorConfigGenerator;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\OperatorTypeDefinitionInterface;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;

abstract class Base implements OperatorTypeDefinitionInterface
{
    /**
     * @var Service
     */
    protected $graphQlService;

    public function __construct(Service $graphQlService)
    {
        $this->graphQlService = $graphQlService;
    }

    /**
     * @param string $typeName
     * @param array $nodeDef
     * @param ClassDefinition|null $class
     * @param object|null $container
     * @param array $params
     *
     * @return Type
     */
    public function getGraphQlType($typeName, $nodeDef, $class = null, $container = null, $params = [])
    {
        return Type::string();
    }

    /**
     * @param string $typeName
     * @param array $nodeDef
     * @param ClassDefinition|null $class
     * @param object|null $container
     * @param array $params
     *
     * @return mixed
     */
    public function getGraphQlQueryOperatorConfig($typeName, $nodeDef, $class = null, $container = null, $params = [])
    {
        $attributes = $nodeDef['attributes'];
        $label = (isset($attributes['label']) ? $attributes['label'] : '#' . uniqid());
        $label = lcfirst($label);
        $fieldname = preg_replace('/[^A-Za-z0-9\-\.~_]+/', '_', $label);

        $type = $this->getGraphQlType($typeName, $nodeDef, $class, $container, $params);

        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Base($typeName, $attributes, $class, $container);
        $resolver->setGraphQlService($this->graphQlService);

        return $this->enrichConfig([
            'name' => $fieldname,
            'type' => $type,
            'resolve' => [$resolver, 'resolve']

        ], $container);
    }

    /**
     * @param array $config
     * @param object|null $container
     *
     * @return mixed
     */
    public function enrichConfig($config, $container = null)
    {
        if ($container instanceof Data\Localizedfields) {
            $config['args'] = $config['args'] ? $config['args'] : [];
            $config['args'] = array_merge($config['args'],
                ['language' => ['type' => Type::string()]
            ]);
        }

        return $config;
    }

    /**
     * @param array $attributes
     * @param ClassDefinition|null $class
     * @param object|null $container
     *
     * @return \GraphQL\Type\Definition\ScalarType
     */
    public function getFieldType($attributes, $class = null, $container = null)
    {
        return Type::string();
    }
}
