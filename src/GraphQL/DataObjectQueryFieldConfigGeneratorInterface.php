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

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;

interface DataObjectQueryFieldConfigGeneratorInterface
{
    /**
     * @param string $columnConfig
     * @param Data $fieldDefinition
     * @param ClassDefinition $class
     * @param object $container
     *
     * @return mixed
     */
    public function getGraphQlFieldConfig($columnConfig, Data $fieldDefinition, $class, $container);

    /**
     * @param Data $fieldDefinition
     * @param ClassDefinition|null $class
     * @param object|null $container
     *
     * @return mixed
     */
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null);

    /**
     * @param string $attribute
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|null $class
     *
     * @return array|callable(mixed $value, array $args, array $context, \GraphQL\Type\Definition\ResolveInfo $info): mixed
     */
    public function getResolver($attribute, $fieldDefinition, $class);
}
