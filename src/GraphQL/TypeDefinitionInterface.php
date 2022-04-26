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

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use GraphQL\Type\Definition\Type;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;

interface TypeDefinitionInterface
{
    /**
     * @param Data $fieldDefinition
     * @param ClassDefinition|null $class
     * @param object|null $container
     *
     * @return Type
     */
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null);
}
