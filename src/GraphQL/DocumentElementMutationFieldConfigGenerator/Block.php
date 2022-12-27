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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementMutationFieldConfigGenerator;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class Block extends Base
{
    /** @var InputObjectType|null */
    public static $itemType;

    /** @var \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor\Block */
    public $processor;

    public function __construct(Service $graphQlService, \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor\Block $processor)
    {
        parent::__construct($graphQlService);
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
                    'name' => 'document_element_input_block_item',
                    'fields' => function () {
                        return [
                            'replace' => [
                                'type' => Type::boolean(),
                                'description' => 'if true (default), all elements inside the block will be replaced'
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
                    'name' => 'document_element_input_block',
                    'fields' => function () {
                        return [
                            '_editableName' => Type::nonNull(Type::string()),
                            'indices' => Type::listOf(Type::int()),
                            'items' => [
                                'type' => Type::listOf(self::$itemType),
                            ]
                        ];
                    }
                ]
            ),
            'processor' => [$this->processor, 'process']
        ];
    }
}
