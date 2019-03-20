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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\OperatorConfigGenerator;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\OperatorTypeDefinitionInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;

abstract class Base implements OperatorTypeDefinitionInterface
{
    /**
     * Base constructor.
     */
    public function __construct()
    {
    }

    public function getGraphQlType($typeName, $nodeDef, $class = null, $container = null, $params = [])
    {
        return Type::string();
    }

    /**
     * @param string $typeName
     * @param mixed $attributes
     * @param null $class
     * @param null $container
     *
     * @return mixed
     */
    public function getGraphQlOperatorConfig($typeName, $nodeDef, $class = null, $container = null, $params = [])
    {
        $attributes = $nodeDef['attributes'];
        $label = ($attributes['label'] ? $attributes['label'] : '#' . uniqid());
        $label = strtolower($label);
        $fieldname = preg_replace('/[^a-z0-9\-\.~_]+/', '_', $label);

        $type = $this->getGraphQlType($typeName, $nodeDef, $class, $container, $params);

        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Base($typeName, $attributes, $class, $container);

        return $this->enrichConfig([
            'name' => $fieldname,
            'type' => $type,
            'resolve' => [$resolver, "resolve"]

        ], $container);
    }

    /**
     * @param $config
     * @param $container
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
     * @param $attributes
     * @param null $class
     * @param null $container
     *
     * @return \GraphQL\Type\Definition\ListOfType|mixed
     */
    public function getFieldType($attributes, $class = null, $container = null)
    {
        return Type::string();
    }
}
