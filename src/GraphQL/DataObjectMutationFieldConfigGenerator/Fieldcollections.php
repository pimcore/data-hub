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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationFieldConfigGenerator;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields;
use Pimcore\Model\DataObject\Fieldcollection\Definition;

class Fieldcollections extends Base
{
    /** @var array */
    public static $typeCache = [];

    /** {@inheritdoc } */
    public function getGraphQlMutationFieldConfig($nodeDef, $class, $container = null, $params = [])
    {
        $fieldName = $nodeDef['attributes']['attribute'];

        /** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections $fcDef */
        $fcDef = $class->getFieldDefinition($fieldName);

        $allowedFcTypes = $fcDef->getAllowedTypes();
        if (!$allowedFcTypes) {
            $list = new Definition\Listing();
            $list = $list->load();

            $allowedFcTypes = [];
            foreach ($list as $fcDef) {
                $allowedFcTypes[] = $fcDef->getKey();
            }
        }

        $groupsInputTypeName = 'fieldcollections_' . $fieldName . '_groups_input';

        $groupsInputFields = [];

        $fieldProcessors = [];

        foreach ($allowedFcTypes as $allowedFcType) {
            $fcDef = Definition::getByKey($allowedFcType);

            $listInputTypeName = 'fieldcollections_' . $fieldName . '_' . $allowedFcType . '_input';

            $inputFields = [];

            $this->generateInputFieldsAndProcessors($inputFields, $processors, $fcDef);

            $inputFields['index'] = Type::int();

            // groups
            $listItemType = new InputObjectType([
                'name' => $listInputTypeName,
                'fields' => $inputFields
            ]);
            $groupsInputFields[$allowedFcType] = Type::listOf($listItemType);
            $fieldProcessors[$allowedFcType] = $processors;
        }

        $groupsInputType = new InputObjectType([
            'name' => $groupsInputTypeName,
            'fields' => $groupsInputFields
        ]);

        $inputTypeName = 'fieldcollections_' . $fieldName . '_input';

        $inputType = self::$typeCache[$inputTypeName] ?? null;

        if (!$inputType) {
            $inputType = new InputObjectType([
                'name' => $inputTypeName,
                'fields' => [
                    'replace' => [
                        'type' => Type::boolean(),
                        'description' => 'if true then the entire item list will be overwritten'
                    ],
                    'items' => $groupsInputType
                ]
            ]);
            self::$typeCache[$inputTypeName] = $inputType;
        }

        $processor = new \Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor\Fieldcollections($nodeDef, $fieldProcessors);
        $processor->setGraphQLService($this->getGraphQlService());

        return [
            'arg' => $inputType,
            'processor' => [$processor, 'process']
        ];
    }

    /**
     * @param array $inputFields
     * @param array $processors
     * @param Definition $fcDef
     */
    public function generateInputFieldsAndProcessors(&$inputFields, &$processors, Definition $fcDef)
    {
        $fcFieldDefinitions = $fcDef->getFieldDefinitions();
        $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();

        foreach ($fcFieldDefinitions as $fcFieldDef) {
            if ($fcFieldDef instanceof Localizedfields) {
                $localizedDefs = $fcFieldDef->getFieldDefinitions();
                foreach ($localizedDefs as $localizedDef) {
                    $nodeDef = [
                        'attributes' => [
                            'attribute' => $localizedDef->getName(),
                            'label' => $localizedDef->getTitle() ?? $localizedDef->getName(),
                            'dataType' => $localizedDef->getFieldtype(),
                            'layout' => $localizedDef
                        ]
                    ];
                    $result = $fieldHelper->getMutationFieldConfigFromConfig($nodeDef, $fcDef);
                    if ($result) {
                        $inputFields[$result['key']] = $result['arg'];
                        $processor = $result['processor'];
                        $processors[$result['key']] = $processor;
                    }
                }
            } else {
                $nodeDef = [
                    'attributes' => [
                        'attribute' => $fcFieldDef->getName(),
                        'label' => $fcFieldDef->getTitle() ?? $fcFieldDef->getName(),
                        'dataType' => $fcFieldDef->getFieldtype(),
                        'layout' => $fcFieldDef
                    ]
                ];

                $result = $fieldHelper->getMutationFieldConfigFromConfig($nodeDef, $fcDef);
                if ($result) {
                    $inputFields[$result['key']] = $result['arg'];
                    $processor = $result['processor'];
                    $processors[$result['key']] = $processor;
                }
            }
        }
    }
}
