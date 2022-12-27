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

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Classificationstore\GroupConfig;

class Classificationstore extends Base
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
            'args' => ['language' => ['type' => Type::string()]],
            'description' => 'returns a list of group containers',
            'resolve' => function ($value, $args, $context = [], ResolveInfo $resolveInfo = null) {
                $fieldName = $resolveInfo->fieldName;
                $language = isset($args['language']) ? $args['language'] : null;
                /** @var \Pimcore\Model\DataObject\Classificationstore $csField */
                $csField = $value[$fieldName];

                $fd = new Data\Classificationstore();
                $fd->setName($fieldName);
                $activeGroups = [];
                $activeGroups = $fd->recursiveGetActiveGroupsIds($csField->getObject(), $activeGroups);

                $result = [];
                foreach ($activeGroups as $groupId => $enabled) {
                    // in case group name and description is not needed this can be optimized
                    // analyze the resolveInfo
                    $groupConfig = GroupConfig::getById($groupId);

                    if ($groupConfig) {
                        $result[] = [
                                'id' => $groupId,
                                'name' => $groupConfig->getName(),
                                'description' => $groupConfig->getDescription(),
                                '_csValue' => $csField,
                                '_language' => $language
                            ];
                    }
                }

                return $result;
            }
        ], $container);
    }

    /**
     * @param Data $fieldDefinition
     * @param ClassDefinition|null $class
     * @param object|null $container
     *
     * @return ListOfType
     */
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        $service = $this->getGraphQlService();
        $groupType = $service->getClassificationStoreTypeDefinition('cs_group');

        return Type::listOf($groupType);
    }
}
