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
declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator\Factory\OperatorFactoryInterface;
use Pimcore\Bundle\DataHubBundle\GraphQL\Query\Value\DefaultValue;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Cache\Runtime;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Psr\Container\ContainerInterface;

class Service
{
    /***
     * @var ContainerInterface
     */
    private $queryTypeGeneratorFactories;

    /**
     * @var ContainerInterface
     */
    private $queryOperatorFactories;

    /**
     * @var array
     */
    private $supportedQueryDataTypes;

    /**
     * Service constructor.
     *
     * @param ContainerInterface $queryTypeGeneratorFactories
     * @param ContainerInterface $queryOperatorFactories
     */
    public function __construct(
        ContainerInterface $queryTypeGeneratorFactories,
        ContainerInterface $queryOperatorFactories
    ) {
        $this->queryTypeGeneratorFactories = $queryTypeGeneratorFactories;
        $this->queryOperatorFactories = $queryOperatorFactories;
    }

    /**
     * @param $name
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|null $class
     * @param null $container
     *
     * @return mixed
     */
    public function buildDataQueryConfig($name, Data $fieldDefinition = null, ClassDefinition $class = null, $container = null)
    {

        /** @var FieldConfigGeneratorInterface $factory */
        $factory = $this->queryTypeGeneratorFactories->get('typegenerator_datatype_' . $name);
        $result = $factory->getGraphQlFieldConfig($fieldDefinition, $class, $container);

        return $result;
    }

    /**
     * @param $name
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|null $class
     * @param null $container
     *
     * @return mixed
     */
    public function buildDataQueryResolver(Data $fieldDefinition = null, ClassDefinition $class = null)
    {
        $name = $fieldDefinition->getFieldtype();
        /** @var FieldConfigGeneratorInterface $factory */
        $factory = $this->queryTypeGeneratorFactories->get('typegenerator_datatype_' . $name);
        $resolver = $factory->getResolver($fieldDefinition, $class);

        return $resolver;
    }

    /**
     * @param $name
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|null $class
     * @param null $container
     *
     * @return mixed
     */
    public function buildDataQueryType(Data $fieldDefinition = null, ClassDefinition $class = null, $container = null)
    {
        $name = $fieldDefinition->getFieldtype();
        /** @var FieldConfigGeneratorInterface $factory */
        $factory = $this->queryTypeGeneratorFactories->get('typegenerator_datatype_' . $name);
        $result = $factory->getFieldType($fieldDefinition, $class, $container);

        return $result;
    }

    /**
     * @param $typeName
     *
     * @return bool
     */
    public function supportsDataQueryType($typeName)
    {
        return $this->queryTypeGeneratorFactories->has('typegenerator_datatype_' . $typeName);
    }

    /**
     * @param $typeName
     * @param $nodeDef
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|null $class
     * @param null $container
     *
     * @return mixed
     */
    public function buildOperatorQueryConfig($typeName, $nodeDef, ClassDefinition $class = null, $container = null, $params = [])
    {
        $typeName = strtolower($typeName);
        /** @var FieldConfigGeneratorInterface $factory */
        $factory = $this->queryTypeGeneratorFactories->get('typegenerator_operator_' . $typeName);
        $result = $factory->getGraphQlOperatorConfig($typeName, $nodeDef, $class, $container, $params);

        return $result;
    }

    /**
     * @param $typeName
     * @param $nodeDef
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|null $class
     * @param null $container
     *
     * @return mixed
     */
    public function buildOperatorQueryType($typeName, $nodeDef, ClassDefinition $class = null, $container = null, $params = [])
    {
        $typeName = strtolower($typeName);
        /** @var FieldConfigGeneratorInterface $factory */
        $factory = $this->queryTypeGeneratorFactories->get('typegenerator_operator_' . $typeName);
        $result = $factory->getGraphQlOperatorConfig($typeName, $nodeDef, $class, $container, $params);

        return $result;
    }

    /**
     * @param $typeName
     *
     * @return bool
     */
    public function supportsOperatorQueryType($typeName)
    {
        return $this->queryTypeGeneratorFactories->has('typegenerator_operator_' . $typeName);
    }

    /**
     * @param $typeName
     * @param $attributes
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|null $class
     * @param null $container
     *
     * @return mixed
     */
    public function buildOperator($typeName, $attributes = null, ClassDefinition $class = null, $container = null)
    {
        $typeName = strtolower($typeName);
        /** @var OperatorFactoryInterface $factory */
        $factory = $this->queryOperatorFactories->get($typeName);

        $context = Runtime::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        $result = $factory->build($attributes, $context);

        return $result;
    }

    /**
     * @param $typeName
     *
     * @return bool
     */
    public function supportsOperator($typeName)
    {
        $typeName = strtolower($typeName);

        return $this->queryOperatorFactories->has('query_operator_' . $typeName);
    }

    public function buildValueResolverFromAttributes($nodeConfig)
    {
        $attributes = $nodeConfig['attributes'];
        if ($nodeConfig['isOperator']) {
            $class = $attributes['class'];
            $operatorImpl = $this->buildOperator($class, $attributes);

            return $operatorImpl;
        } else {
            $context = Runtime::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
            $operatorImpl = new DefaultValue($attributes, $context);

            return $operatorImpl;
        }
    }

    /**
     * @return array
     */
    public function getSupportedQueryDataTypes()
    {
        return $this->supportedQueryDataTypes;
    }

    /**
     * @param $supportedQueryDataTypes
     */
    public function setSupportedQueryDataTypes($supportedQueryDataTypes)
    {
        $this->supportedQueryDataTypes = $supportedQueryDataTypes;
    }
}
