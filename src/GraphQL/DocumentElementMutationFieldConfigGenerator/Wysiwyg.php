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
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

/**
 * @internal
 */
final class Wysiwyg extends Base
{
    /** @var \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor\Input */
    public $processor;

    public function __construct(Service $graphQlService, \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor\Input $processor)
    {
        parent::__construct($graphQlService);
        $this->processor = $processor;
    }

    /**
     * @return array
     */
    public function getDocumentElementMutationFieldConfig()
    {
        return [
            'arg' => new InputObjectType(
                [
                    'name' => 'document_element_input_wysiwyg',
                    'fields' => [
                        '_editableName' => Type::nonNull(Type::string()),
                        'text' => Type::string(),
                    ],
                ]
            ),
            'processor' => [$this->processor, 'process'],
        ];
    }
}
