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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementMutationFieldConfigGenerator;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\AreablockDataInputType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class Areablock extends Base
{

    /** @var InputObjectType */
    static $itemType;

    protected $areablockDataInputType;

    /**
     * Areablock constructor.
     * @param Service $graphQlService
     * @param AreablockDataInputType $areablockDataInputType
     */
    public function __construct(Service $graphQlService, AreablockDataInputType $areablockDataInputType)
    {
        $this->setGraphQLService($graphQlService);
        $this->areablockDataInputType = $areablockDataInputType;
    }

    /**
     */
    public function getDocumentElementMutationFieldConfig()
    {
        $processor = new \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor\Areablock();
        $processor->setGraphQLService($this->getGraphQlService());

        if (!self::$itemType) {
            self::$itemType = new InputObjectType(
                [
                    'name' => 'document_element_input_areablock_item',
                    'fields' => function () {
                        return [
                            'type' => Type::nonNull(Type::string()),
                            'hidden' => Type::boolean(),
                            'replace' => [
                                "type" => Type::boolean(),
                                "description" => "if true (default), all elements inside the block will be replaced"
                            ],
                            'editables' => MutationType::$documentElementTypes
                        ];
                    }
                ]
            );
        }

        return [
            'arg' => new InputObjectType(
                [
                    'name' => 'document_element_input_areablock',
                    'fields' => function () {
                        return [
                            '_tagName' => Type::nonNull(Type::string()),
                            'indices' => Type::listOf($this->areablockDataInputType),
                            'items' => [
                                'type' => Type::listOf(self::$itemType),
                            ]
                        ];
                    }
                ]

            ),
            'processor' => [$processor, 'process']
        ];
    }

}
