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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\BlockDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\BlockEntryType;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Data\BlockElement;

class Block extends Base
{
    /**
     * @param      $attribute
     * @param Data $fieldDefinition
     * @param null $class
     * @param null $container
     *
     * @return mixed
     */
    public function getGraphQlFieldConfig($attribute, Data $fieldDefinition, $class = null, $container = null)
    {
        return $this->enrichConfig($fieldDefinition, $class, $attribute, [
            'name' => $fieldDefinition->getName(),
            'type' => $this->getFieldType($fieldDefinition, $class, $container),
            'resolve' => $this->getResolver($attribute, $fieldDefinition, $class),
        ], $container);
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
        return Type::listOf(new BlockEntryType($this->getGraphQlService(), $fieldDefinition, $class, []));
    }

    /**
     * @param array $attribute
     * @param Data $fieldDefinition
     * @param      $class
     *
     * @return \Closure
     */
    public function getResolver($attribute, $fieldDefinition, $class)
    {
        return function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) use (
            $fieldDefinition
        ) {
            $fieldname = $fieldDefinition->getName();
            $result = [];
            foreach ($value[$fieldname] as $blockIndex => $blockEntries) {
                foreach ($blockEntries as $key => $blockValue) {
                    if (!$blockValue instanceof BlockElement) {
                        continue;
                    }

                    $subDef = $fieldDefinition->getFieldDefinition($key);

                    if (!$subDef) {
                        continue;
                    }

                    $blockDescriptor = new BlockDescriptor();
                    $blockDescriptor['id'] = $value['id'];
                    $blockDescriptor['__blockName'] = $fieldDefinition->getName();
                    $blockDescriptor['__blockIndex'] = $blockIndex;
                    $blockDescriptor['__blockFieldName'] = $key;

                    if ($subDef instanceof Data\Localizedfields) {
                        foreach ($subDef->getChildren() as $localizedDef) {
                            $blockDescriptor['__localized'] = $localizedDef->getName();
                        }
                    }

                    $result[$blockIndex][$key] = $blockDescriptor;
                }
            }

            return $result;
        };
    }
}
