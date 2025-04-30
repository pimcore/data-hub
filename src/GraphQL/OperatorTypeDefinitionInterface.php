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

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use Pimcore\Model\DataObject\ClassDefinition;

interface OperatorTypeDefinitionInterface
{
    /**
     * @param array $attributes
     * @param ClassDefinition $class
     * @param object $container
     *
     * @return mixed
     */
    public function getFieldType($attributes, $class, $container);
}
