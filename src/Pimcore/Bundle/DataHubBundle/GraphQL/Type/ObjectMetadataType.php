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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Data\ElementMetadata;

class ObjectMetadataType extends ObjectType
{
    protected $class;

    protected $fieldDefinition;

    /**
     * PimcoreObjectType constructor.
     *
     * @param $class
     */
    public function __construct(Data $fieldDefinition = null, $class = null, $config = [])
    {
        $this->class = $class;
        $this->fieldDefinition = $fieldDefinition;
        $config['name'] = 'object_'.$class->getName().'_'.$fieldDefinition->getName();
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $fieldHelper = \Pimcore::getContainer()->get('pimcore.datahub.graphql.fieldhelper.object');
        /** @var Data\AdvancedManyToManyObjectRelation $fieldDefinition */
        $fieldDefinition = $this->fieldDefinition;
        $class = $this->class;

        $className = $fieldDefinition->getAllowedClassId();
        $elementTypeDefinition = ClassTypeDefinitions::get($className);

        $fields = ['element'  =>
                       [
                           'type'    => $elementTypeDefinition,
                           'resolve' => function (
                               $value = null,
                               $args = [],
                               $context,
                               ResolveInfo $resolveInfo = null
                           ) use (
                               $fieldDefinition,
                               $class,
                                $fieldHelper
                           ) {
                               $element = null;

                               if (!$value['element']) {
                                   return null;
                               }

                               if ($value['element']['__elementType'] == 'object') {
                                   $element = AbstractObject::getById($value['element']['__destId']);
                               } else {
                                   if ($value['element']['__elementType'] == 'asset') {
                                       $element = Asset::getById($value['element']['__destId']);
                                   }
                               }

                               if (!$element) {
                                   return null;
                               }

                               $data = $value['element'];
                               $fieldHelper->extractData($data, $element, $args, $context, $resolveInfo);

                               return $data;
                           },

                       ],
                   'metadata' => [
                       'type'    => Type::listOf(new ElementMetadataKeyValuePairType()),
                       'resolve' => function ($value = null, $args = [], $context, ResolveInfo $resolveInfo = null) {
                           if ($value && $value['element']) {

                               /** @var $relation ElementMetadata */
                               $relation = $value['element']['__relation'];
                               $meta = $relation->getData();
                               $result = [];
                               if ($meta) {
                                   foreach ($meta as $metaItemKey => $metaItemValue) {
                                       $result[] = [
                                           'name'  => $metaItemKey,
                                           'value' => $metaItemValue,
                                       ];
                                   }
                               }

                               return $result;
                           }

                           return null;
                       },
                   ]];

        $config['fields'] = $fields;

        return;
    }
}
