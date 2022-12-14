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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationFieldConfigGenerator;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\ElementDescriptorInputType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class AdvancedManyToManyRelation extends Base
{
    protected $elementInputType;
    protected $fieldDefinition;

    public function __construct(Service $graphQlService, ElementDescriptorInputType $elementInputType)
    {
        $this->elementInputType = $elementInputType;
        parent::__construct($graphQlService);
    }

    /** {@inheritdoc } */
    public function getGraphQlMutationFieldConfig($nodeDef, $class, $container = null, $params = [])
    {
        $processor = new \Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor\AdvancedManyToManyRelation($nodeDef);
        $processor->setGraphQLService($this->getGraphQlService());

        $inputType = $this->getGraphQlService()->getDataObjectTypeDefinition('elementdescriptor_input');

        return [
            'arg' => ['type' => Type::listOf($inputType)],
            'processor' => [$processor, 'process']
        ];
    }
}
