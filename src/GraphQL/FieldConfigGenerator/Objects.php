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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldConfigGenerator;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Type\HrefType;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeDefinitionInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;

/**
 * Class Multihref
 *
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\FieldConfigGenerator
 */
class Objects extends Base implements TypeDefinitionInterface
{
    /**
     * @param Data $fieldDefinition
     * @param null $class
     * @param null $container
     *
     * @return mixed
     */
    public function getGraphQlFieldConfig(Data $fieldDefinition, $class = null, $container = null)
    {
        return $this->enrichConfig(
            [
                'name'    => $fieldDefinition->getName(),
                'type'    => $this->getFieldType($fieldDefinition, $class, $container),
                'resolve' => $this->getResolver($fieldDefinition, $class)
            ],
            $container
        );
    }

    /**
     * @param Data $fieldDefinition
     * @param null $class
     * @param null $container
     *
     * @return \GraphQL\Type\Definition\ListOfType|mixed
     */
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        return Type::listOf(new HrefType($fieldDefinition, $class));
    }

    /**
     * @param Data $fieldDefinition
     * @param $class
     *
     * @return \Closure
     */
    public function getResolver($fieldDefinition, $class)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\FieldConfigGenerator\Helper\Objects($this->graphQlService, $fieldDefinition, $class);
        return [$resolver, "resolve"];
    }
}
