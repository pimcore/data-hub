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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementMutationFieldConfigGenerator;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class Select extends Base
{

    /**
     */
    public function getDocumentElementMutationFieldConfig()
    {
        $processor = new \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor\Select();
        $processor->setGraphQLService($this->getGraphQlService());

        return [
            'arg' => new InputObjectType(
                [
                    'name' => 'document_element_input_select',
                    'fields' => [
                        '_tagName' => Type::nonNull(Type::string()),
                        'text' => Type::string(),
                    ]
                ]
            ),
            'processor' => [$processor, 'process']
        ];
    }

}
