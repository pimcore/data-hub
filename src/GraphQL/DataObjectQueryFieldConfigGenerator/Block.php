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

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\BlockDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\BlockEntryType;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldcollectionDescriptor;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\Objectbrick\Definition;
use Pimcore\Model\DataObject\Service;

class Block extends Base
{
    /**
     * @param string $attribute
     * @param Data $fieldDefinition
     * @param ClassDefinition|null $class
     * @param object|null $container
     *
     * @return array
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
     * @param ClassDefinition|null $class
     * @param object|null $container
     *
     * @return \GraphQL\Type\Definition\ListOfType
     */
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        return Type::listOf(new BlockEntryType($this->getGraphQlService(), $fieldDefinition, $class, []));
    }

    /**
     * @param string $attribute
     * @param Data $fieldDefinition
     * @param ClassDefinition $class
     *
     * @return \Closure
     */
    public function getResolver($attribute, $fieldDefinition, $class)
    {
        return function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) use (
            $fieldDefinition, $attribute
        ) {
            $originalValue = $value;
            $result = [];
            $isBrick = false;
            $attributeParts = explode('~', $attribute);
            $fieldname = $fieldDefinition->getName();
            $brickDescriptor = null;
            $brickType = null;
            $brickKey = null;

            if (count($attributeParts) > 1) {
                $id = $value['id'];
                $object = Concrete::getById($id);

                if (!$object) {
                    return null;
                }

                $context = ['object' => $object];

                $brickType = $attributeParts[0];
                if (strpos($brickType, '?') !== false) {
                    $brickDescriptor = substr($brickType, 1);
                    $brickDescriptor = json_decode($brickDescriptor, true);
                    $brickType = $brickDescriptor['containerKey'];
                }
                $brickKey = $attributeParts[1];

                $key = Service::getFieldForBrickType($object->getclass(), $brickType);

                $brickClass = Definition::getByKey($brickType);

                if (!$brickClass) {
                    return null;
                }

                $context['outerFieldname'] = $key;

                $def = $brickClass->getFieldDefinition($brickKey, $context);

                if (!$def) {
                    return null;
                }

                $isBrick = true;

                if (!empty($key)) {
                    $value = \Pimcore\Bundle\DataHubBundle\GraphQL\Service::getValueForObject($object, $key, $brickType, $brickKey, $def, $context, $brickDescriptor, $args);
                    $fieldDefinition = $def;
                }
            } else {
                $id = $value['id'];
                $value = $value[$fieldname];
            }

            if (is_callable($value)) {
                $value = $value($value, $args, $context, $resolveInfo);
            }

            if (!$value) {
                return null;
            }

            foreach ($value as $blockIndex => $blockEntries) {
                foreach ($blockEntries as $key => $blockValue) {
                    if (!$blockValue instanceof BlockElement) {
                        continue;
                    }

                    $subDef = $fieldDefinition->getFieldDefinition($key);

                    if (!$subDef) {
                        continue;
                    }

                    if ($subDef instanceof Data\Localizedfields) {
                        foreach ($subDef->getChildren() as $localizedDef) {
                            $blockDescriptor = new BlockDescriptor();
                            $blockDescriptor['id'] = $id;
                            $blockDescriptor['__blockName'] = $fieldDefinition->getName();
                            $blockDescriptor['__blockIndex'] = $blockIndex;
                            $blockDescriptor['__blockFieldName'] = $key;
                            $blockDescriptor['__localized'] = $localizedDef->getName();
                            $blockDescriptor['args'] = $args;

                            if ($originalValue instanceof FieldcollectionDescriptor) {
                                $blockDescriptor['__fcFieldname'] = $originalValue['__fcFieldname'];
                                $blockDescriptor['__fcType'] = $originalValue['__fcType'];
                                $blockDescriptor['__itemIdx'] = $originalValue['__itemIdx'];
                            } elseif ($isBrick) {
                                $blockDescriptor['__brickDescriptor'] = $brickDescriptor;
                                $blockDescriptor['__brickType'] = $brickType;
                                $blockDescriptor['__brickKey'] = $brickKey;
                            }

                            $result[$blockIndex][$localizedDef->getName()] = $blockDescriptor;
                        }

                        continue;
                    }

                    $blockDescriptor = new BlockDescriptor();
                    $blockDescriptor['id'] = $id;
                    $blockDescriptor['__blockName'] = $fieldDefinition->getName();
                    $blockDescriptor['__blockIndex'] = $blockIndex;
                    $blockDescriptor['__blockFieldName'] = $key;
                    $blockDescriptor['args'] = $args;

                    if ($originalValue instanceof FieldcollectionDescriptor) {
                        $blockDescriptor['__fcFieldname'] = $originalValue['__fcFieldname'];
                        $blockDescriptor['__fcType'] = $originalValue['__fcType'];
                        $blockDescriptor['__itemIdx'] = $originalValue['__itemIdx'];
                    } elseif ($isBrick) {
                        $blockDescriptor['__brickDescriptor'] = $brickDescriptor;
                        $blockDescriptor['__brickType'] = $brickType;
                        $blockDescriptor['__brickKey'] = $brickKey;
                    }

                    $result[$blockIndex][$key] = $blockDescriptor;
                }
            }

            return $result;
        };
    }
}
