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

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\MergeType;
use Pimcore\Model\DataObject\Localizedfield;

class Merge extends StringBase
{
    protected function getFieldname($attributes)
    {
        $label = ($attributes['label'] ? $attributes['label'] : '#'.uniqid());
        $label = lcfirst($label);
        $fieldname = preg_replace('/[^A-Za-z0-9\-\.~_]+/', '_', $label);

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
    public function getGraphQlQueryOperatorConfig($typeName, $nodeConfig, $class = null, $container = null, $params = [])
    {
        $attributes = $nodeConfig['attributes'];
        $fieldname = $this->getFieldname($attributes);

        $type = $this->getGraphQlType($typeName, $nodeConfig, $class, $container, $params);
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Merge($typeName, $attributes, $class, $container);
        $resolver->setGraphQlService($this->graphQlService);

        return $this->enrichConfig(
            [
                'name' => $fieldname,
                'type' => $type,
                'resolve' => [$resolver, 'resolve']
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

        $mergeType = new MergeType($this->graphQlService, $nodeDef, $class, $container, ['name' => $typename]);

        $result = ListOfType::listOf($mergeType);

        return $result;
    }
}
