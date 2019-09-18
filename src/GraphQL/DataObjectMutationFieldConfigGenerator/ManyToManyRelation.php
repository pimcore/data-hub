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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationFieldConfigGenerator;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\ElementDescriptorInputType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class ManyToManyRelation extends Base
{

    protected $elementInputType;

    /**
     * ManyToManyRelation constructor.
     * @param Service $graphQlService
     * @param ElementDescriptorInputType $elementInputType
     */
    public function __construct(Service$graphQlService, ElementDescriptorInputType $elementInputType)
    {
        $this->elementInputType = $elementInputType;
        parent::__construct($graphQlService);
    }


    /**
     * @param $nodeDef
     * @param $class
     * @param $container
     * @return array
     * @throws \Exception
     */
    public function getGraphQlMutationFieldConfig($nodeDef, $class, $container = null)
    {
        $processor = new \Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor\ManyToManyRelation($nodeDef);
        $processor->setGraphQLService($this->getGraphQlService());

        $inputType = $this->getGraphQlService()->getDataObjectTypeDefinition("elementdescriptor_input");
        return [
            'arg' => ["type" => Type::listOf($inputType)],
            'processor' => [$processor, 'process']
        ];
    }

}
