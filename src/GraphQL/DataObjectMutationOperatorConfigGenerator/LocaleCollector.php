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

use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor\LocaleCollectorOperator;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\LocalizedType;

class LocaleCollector extends Base
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
            'processor' => [$processor, 'process']
        ];
    }
}
