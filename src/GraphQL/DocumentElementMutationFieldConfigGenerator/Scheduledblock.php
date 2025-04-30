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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementMutationFieldConfigGenerator;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\ScheduledblockDataInputType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

/**
 * @internal
 */
final class Scheduledblock extends Base
{
    /** @var InputObjectType|null */
    public static $itemType;

    /** @var ScheduledblockDataInputType */
    protected $scheduledblockDataInputType;

    /** @var \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor\Scheduledblock */
    protected $processor;

    public function __construct(Service $graphQlService, ScheduledblockDataInputType $scheduledblockDataInputType, \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor\Scheduledblock $processor)
    {
        $this->setGraphQLService($graphQlService);
        $this->scheduledblockDataInputType = $scheduledblockDataInputType;
        $this->processor = $processor;
    }

    /**
     * @return array
     */
    public function getDocumentElementMutationFieldConfig()
    {
        if (!self::$itemType) {
            self::$itemType = new InputObjectType(
                [
                    'name' => 'document_element_input_scheduledblock_item',
                    'fields' => function () {
                        return [
                            'date' => Type::int(),
                            'replace' => [
                                'type' => Type::boolean(),
                                'description' => 'if true (default), all elements inside the block will be replaced',
                            ],
                            'editables' => MutationType::$documentElementTypes,
                        ];
                    },
                ]
            );
        }

        return [
            'arg' => new InputObjectType(
                [
                    'name' => 'document_element_input_scheduledblock',
                    'fields' => function () {
                        return [
                            '_editableName' => Type::nonNull(Type::string()),
                            'indices' => Type::listOf($this->scheduledblockDataInputType),
                            'items' => [
                                'type' => Type::listOf(self::$itemType),
                            ],
                        ];
                    },
                ]

            ),
            'processor' => [$this->processor, 'process'],
        ];
    }
}
