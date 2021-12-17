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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class InputQuantityValueType extends QuantityValueType
{
    /**
     * @param Service $graphQlService
     * @param Data|null $fieldDefinition
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, Data $fieldDefinition = null, $config = [], $context = [])
    {
        $config['fields'] = [
            'value' => [
                'type' => Type::string()
            ]
        ];
        parent::__construct($graphQlService, $fieldDefinition, $config, $context);
    }
}
