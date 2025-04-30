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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationOperatorConfigGenerator;

use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor\LocaleCollectorOperator;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\LocalizedType;

/**
 * @internal
 */
final class LocaleCollector extends Base
{
    /**
     * @param array $nodeDef
     * @param \Pimcore\Model\DataObject\ClassDefinition|null $class
     * @param object|null $container
     * @param array $params
     *
     * @return array
     */
    public function getGraphQlMutationOperatorConfig($nodeDef, $class = null, $container = null, $params = [])
    {
        $processor = new LocaleCollectorOperator($nodeDef);
        $processor->setGraphQLService($this->getGraphQlService());

        $factories = $this->getGraphQlService()->getDataObjectMutationTypeGeneratorFactories();

        $typeName = strtolower($nodeDef['attributes']['class']);
        $factory = $factories->get('typegenerator_dataobjectmutationoperator_' . $typeName);
        $determinedType = LocalizedType::getInstance(
            $factory->resolveInputTypeFromNodeDef($nodeDef, $class, $container)
        );

        return [
            'arg' => $determinedType,
            'processor' => [$processor, 'process'],
        ];
    }
}
