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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationOperatorConfigGenerator;

use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\ClassDefinition;

abstract class Base
{
    use ServiceTrait;

    public function __construct(Service $graphQlService)
    {
        $this->graphQlService = $graphQlService;
    }

    /**
     * @param array $nodeDef
     * @param ClassDefinition $class
     *
     * @return mixed
     */
    public function resolveInputTypeFromNodeDef($nodeDef, ClassDefinition $class)
    {
        $nodeDefAttributes = $nodeDef['attributes'];
        $children = $nodeDefAttributes['children'];

        $firstChild = $children[0];
        $firstChildAttributes = $firstChild['attributes'];
        $service = $this->getGraphQlService();

        $factories = $service->getDataObjectMutationTypeGeneratorFactories();

        if ($firstChild['isOperator']) {
            //  we only support the simple case with one child
            $operatorClass = $firstChildAttributes['class'];
            $typeName = strtolower($operatorClass);
            $mutationConfigGenerator = $factories->get('typegenerator_dataobjectmutationoperator_' . $typeName);
            $result = $mutationConfigGenerator->resolveInputTypeFromNodeDef($firstChild, $class);
        } else {
            $typeName = $firstChildAttributes['dataType'];
            $mutationConfigGenerator = $factories->get('typegenerator_dataobjectmutationdatatype_' . $typeName);
            $config = $mutationConfigGenerator->getGraphQlMutationFieldConfig($firstChild, $class);
            $result = $config['arg'];
        }

        return $result;
    }

    /**
     * @param array $nodeDef
     * @param ClassDefinition|null $class
     * @param object|null $container
     * @param array $params
     *
     * @return array
     */
    public function getGraphQlMutationOperatorConfig($nodeDef, $class = null, $container = null, $params = [])
    {
        $processor = new \Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor\BaseOperator($nodeDef);
        $processor->setGraphQLService($this->getGraphQlService());

        $typeName = strtolower($nodeDef['attributes']['class']);

        $factories = $this->getGraphQlService()->getDataObjectMutationTypeGeneratorFactories();
        $factory = $factories->get('typegenerator_' . 'mutation' . 'operator_' . $typeName);
        $determinedType = $factory->resolveInputTypeFromNodeDef($nodeDef, $class, $container);

        return [
            'arg' => $determinedType,
            'processor' => [$processor, 'process']
        ];
    }
}
