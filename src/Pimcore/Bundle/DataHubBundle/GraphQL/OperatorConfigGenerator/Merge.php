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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\OperatorConfigGenerator;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator\Factory\OperatorFactoryInterface;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Type\MergeType;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Localizedfield;

class Merge extends StringBase
{
    /**
     * Base constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function getFieldname($attributes)
    {
        $label = ($attributes['label'] ? $attributes['label'] : '#'.uniqid());
        $label = strtolower($label);
        $fieldname = preg_replace('/[^a-z0-9\-\.~_]+/', '_', $label);

        return $fieldname;
    }

    /**
     * @param string $typeName
     * @param mixed $attributes
     * @param null $class
     * @param null $container
     *
     * @return mixed
     */
    public function getGraphQlOperatorConfig($typeName, $nodeConfig, $class = null, $container = null, $params = [])
    {
        $attributes = $nodeConfig['attributes'];
        $fieldname = $this->getFieldname($attributes);

        $type = $this->getGraphQlType($typeName, $nodeConfig, $class, $container, $params);

        return $this->enrichConfig(
            [
                'name'    => $fieldname,
                'type'    => $type,
                'resolve' => function ($value = null, $args = [], $context, ResolveInfo $resolveInfo = null) use (
                    $typeName,
                    $attributes,
                    $class,
                    $container
                ) {
                    $service = \Pimcore::getContainer()->get(Service::class);
                    /** @var OperatorFactoryInterface $factory */

                    /** @var $operatorImpl \Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator\AbstractOperator */
                    $operatorImpl = $service->buildOperator($typeName, $attributes);

                    $element = AbstractObject::getById($value['id']);
                    $valueFromOperator = $operatorImpl->getLabeledValue($element, $resolveInfo);
                    if ($valueFromOperator) {
                        return $valueFromOperator->value;
                    } else {
                        return null;
                    }
                },

            ],
            $container
        );
    }

    /**
     * @param $config
     * @param $container
     *
     * @return mixed
     */
    public function enrichConfig($config, $container = null)
    {
        if ($container instanceof Localizedfield) {
            $config['args'] = $config['args'] ? $config['args'] : [];
            $config['args'] = array_merge(
                $config['args'],
                ['language' => ['type' => Type::string()],
                ]
            );
        }

        return $config;
    }

    public function getGraphQlType($typeName, $nodeDef, $class = null, $container = null, $params = [])
    {
        $attributes = $nodeDef['attributes'];
        $fieldname = $this->getFieldname($attributes);
        $typename = 'operator_'.$fieldname;

        $mergeType = new MergeType($nodeDef, $class, $container, [
            'name' => $typename
        ]
        );

        $result = ListOfType::listOf($mergeType);

        return $result;
    }
}
