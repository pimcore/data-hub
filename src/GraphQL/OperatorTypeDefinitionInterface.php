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
