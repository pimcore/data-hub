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

use Pimcore\Model\DataObject\ClassDefinition\Data;

interface DataObjectQueryFieldConfigGeneratorInterface
{
    /**
     * @param array $columnConfig
     * @param Data $fieldDefinition
     * @param $class
     * @param $container
     *
     * @return mixed
     */
    public function getGraphQlFieldConfig($columnConfig, Data $fieldDefinition, $class, $container);

    /**
     * @param Data $fieldDefinition
     * @param null $class
     * @param null $container
     *
     * @return mixed
     */
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null);

    /**
     * @param $attribute
     * @param $fieldDefinition
     * @param $class
     *
     * @return mixed
     */
    public function getResolver($attribute, $fieldDefinition, $class);
}
