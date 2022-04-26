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
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class Embed extends Base
{
    /**
     * @var \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor\Embed
     */
    public $processor;

    /**
     * @param Service $graphQlService
     * @param \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor\Embed $processor
     */
    public function __construct(Service $graphQlService, \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor\Embed $processor)
    {
        parent::__construct($graphQlService);
        $this->processor = $processor;
    }

    public function getDocumentElementMutationFieldConfig()
    {
        return [
            'arg' => new InputObjectType(
                [
                    'name' => 'document_element_input_embed',
                    'fields' => [
                        '_editableName' => Type::nonNull(Type::string()),
                        'url' => Type::string(),
                    ]
                ]
            ),
            'processor' => [$this->processor, 'process']
        ];
    }
}
