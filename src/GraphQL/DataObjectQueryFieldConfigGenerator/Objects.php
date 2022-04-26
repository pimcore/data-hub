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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\HrefType;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeDefinitionInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;

/**
 * Class Objects
 *
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator
 */
class Objects extends Base implements TypeDefinitionInterface
{
    /**
     * @param string $attribute
     * @param Data $fieldDefinition
     * @param ClassDefinition|null $class
     * @param object|null $container
     *
     * @return mixed
     */
    public function getGraphQlFieldConfig($attribute, Data $fieldDefinition, $class = null, $container = null)
    {
        return $this->enrichConfig(
            $fieldDefinition,
            $class,
            $attribute,
            [
                'name' => $fieldDefinition->getName(),
                'type' => $this->getFieldType($fieldDefinition, $class, $container),
                'resolve' => $this->getResolver($attribute, $fieldDefinition, $class)
            ],
            $container
        );
    }

    /**
     * @param Data $fieldDefinition
     * @param ClassDefinition|null $class
     * @param object|null $container
     *
     * @return \GraphQL\Type\Definition\ListOfType|mixed
     */
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        return Type::listOf(new HrefType($this->getGraphQlService(), $fieldDefinition, $class));
    }

    public function getResolver($attribute, $fieldDefinition, $class)
    {
        $resolver = new Helper\Objects($this->getGraphQlService(), $attribute, $fieldDefinition, $class);

        return [$resolver, 'resolve'];
    }
}
