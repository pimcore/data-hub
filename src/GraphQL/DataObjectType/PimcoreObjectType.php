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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Error\InvariantViolation;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldcollectionDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeInterface\Element;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Pimcore\Model\DataObject\Fieldcollection\Definition;

class PimcoreObjectType extends ObjectType
{
    use ServiceTrait;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $classId;

    protected static $skipOperators;

    protected $fields;

    /**
     * @param Service $graphQlService
     * @param string $className
     * @param string $classId
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, string $className, $classId, $config = [], $context = [])
    {
        $this->className = $className;
        $this->classId = $classId;
        $this->name = $config['name'] = 'object_' . $className;
        $this->setGraphQLService($graphQlService);
        $config['interfaces'] = [Element::getInstance()];
        parent::__construct($config);
    }

    /**
     * @param array $context
     */
    public function build($context = [])
    {
        $propertyType = $this->getGraphQlService()->buildGeneralType('element_property');
        $objectTreeType = $this->getGraphQlService()->buildGeneralType('object_tree');
        $elementTagType = $this->getGraphQlService()->buildGeneralType('element_tag');

        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\DataObject($this->getGraphQLService());

        // these are the system fields that are always available, maybe move some of them to FieldHelper so that they
        // are only visible if explicitly configured by the user
        $fields = [
            'id' => Type::id(),
            'creationDate' => Type::int(),
            'modificationDate' => Type::int(),
            'objectType' => [
                'type' => Type::string(),
                'resolve' => function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                    $object = \Pimcore\Model\DataObject::getById($value['id']);

                    if ($object) {
                        $result = $object->getType();

                        if ($result) {
                            return $result;
                        }
                    }

                    return null;
                }
            ],
            'index' => [
                'type' => Type::int(),
                'resolve' => [$resolver, 'resolveIndex']
            ],
            'childrenSortBy' => [
                'type' => Type::string(),
                'resolve' => [$resolver, 'resolveChildrenSortBy']
            ],
            'classname' => [
                'type' => Type::string(),
            ],
            'tags' => [
                'type' => Type::listOf($elementTagType),
                'args' => [
                    'name' => ['type' => Type::string()],
                ],
                'resolve' => [$resolver, 'resolveTag']
            ],
            'properties' => [
                'type' => Type::listOf($propertyType),
                'args' => [
                    'keys' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'comma separated list of key names'
                    ]
                ],
                'resolve' => [$resolver, 'resolveProperties']
            ],
            'parent' => [
                'type' => $objectTreeType,
                'resolve' => [$resolver, 'resolveParent'],
            ],
            'children' => [
                'type' => Type::listOf($objectTreeType),
                'args' => [
                    'objectTypes' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'list of object types (object, variant, folder)'
                    ],
                ],
                'resolve' => [$resolver, 'resolveChildren'],
            ],
            '_siblings' => [
                'type' => Type::listOf($objectTreeType),
                'args' => [
                    'objectTypes' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'list of object types (object, variant, folder)'
                    ],
                ],
                'resolve' => [$resolver, 'resolveSiblings'],
            ],
        ];

        if ($context['clientname']) {

            /** @var Configuration $configurationItem */
            $configurationItem = $context['configuration'];

            $queryColumnConfig = $configurationItem->getQueryColumnConfig($this->className);
            $columns = isset($queryColumnConfig['columns']) ? $queryColumnConfig['columns'] : [];

            if ($columns) {
                $class = ClassDefinition::getById($this->classId);
                foreach ($columns as $column) {
                    if ($column['isOperator'] && self::$skipOperators) {
                        continue;
                    }

                    if (!$column['isOperator'] && is_array($column['attributes']) && $column['attributes']['dataType'] == 'fieldcollections') {
                        $this->addFieldCollectionDefs($column, $class, $fields);
                    } else {
                        $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();
                        $result = $fieldHelper->getQueryFieldConfigFromConfig($column, $class);
                        if (is_array($result)) {
                            $fields[$result['key']] = $result['config'];
                        }
                    }
                }
            }
        }

        $this->fields = null;
        ksort($fields);
        $this->config['fields'] = $fields;
    }

    /**
     * @param array $column
     * @param ClassDefinition $class
     * @param array $fields
     *
     * @return void
     *
     * @throws \Exception
     */
    public function addFieldCollectionDefs($column, ClassDefinition $class, &$fields)
    {
        $fieldname = $column['attributes']['attribute'];
        /** @var ClassDefinition\Data\Fieldcollections $fieldDef */
        $fieldDef = $class->getFieldDefinition(($fieldname));
        $allowedFcs = $fieldDef->getAllowedTypes();
        $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();

        $unionTypes = [];

        foreach ($allowedFcs as $allowedFcName) {
            $fcKey = 'graphql_fieldcollection_' . $allowedFcName;
            if (RuntimeCache::isRegistered($fcKey)) {
                $itemFcType = RuntimeCache::get($fcKey);
            } else {
                $fcDef = Definition::getByKey($allowedFcName);
                $fcFields = [];
                if ($fcDef != null) {
                    $fcFieldDefs = $fcDef->getFieldDefinitions();

                    foreach ($fcFieldDefs as $key => $fieldDef) {
                        $attrName = $fieldDef->getName();
                        $columnDesc = [
                            'isOperator' => false,
                            'attributes' => [
                                'attribute' => $attrName,
                                'label' => $fieldDef->getName(),
                                'dataType' => $fieldDef->getFieldtype()
                            ]
                        ];
                        $fcResult = $fieldHelper->getQueryFieldConfigFromConfig($columnDesc, $fcDef);
                        if ($fcResult) {
                            $fcFields[$fcResult['key']] = $fcResult['config'];
                        }
                    }
                }

                $fcLocalizedFields = $fcDef->getFieldDefinition('localizedfields');
                if ($fcLocalizedFields instanceof ClassDefinition\Data\Localizedfields) {
                    $fcLocalizedFieldDefs = $fcLocalizedFields->getFieldDefinitions();

                    foreach ($fcLocalizedFieldDefs as $key => $fieldDef) {
                        $attrName = $fieldDef->getName();

                        $columnDesc = [
                            'isOperator' => false,
                            'attributes' => [
                                'attribute' => $attrName,
                                'label' => $fieldDef->getName(),
                                'dataType' => $fieldDef->getFieldtype()
                            ]
                        ];
                        $fcResult = $fieldHelper->getQueryFieldConfigFromConfig($columnDesc, $fcDef, $fcLocalizedFields);
                        if ($fcResult) {
                            $fcFields[$fcResult['key']] = $fcResult['config'];
                        }
                    }
                }

                $typename = 'fieldcollection_' . $allowedFcName;

                $itemFcType = new ObjectType([
                    'name' => $typename,
                    'fields' => $fcFields
                ]);

                RuntimeCache::save($itemFcType, $fcKey);
            }

            $unionTypes[] = $itemFcType;
        }

        $unionname = 'object_' . $this->className . '_' . $fieldname;

        $unionTypesConfig = [
            'name' => $unionname,
            'types' => $unionTypes
        ];

        $union = new FieldcollectionType($this->getGraphQlService(), $unionTypesConfig);

        $fields[$fieldname] =
            [
                'name' => $fieldname,
                'type' => Type::listOf($union),
                'resolve' => function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) use ($fieldname) {
                    if ($value[$fieldname] instanceof Fieldcollection) {
                        $lofItems = [];
                        $fcData = $value[$fieldname];

                        $items = $fcData->getItems();
                        if ($items) {
                            $idx = -1;

                            /** @var AbstractData $item */
                            foreach ($items as $item) {
                                $idx++;
                                $data = new FieldcollectionDescriptor();
                                $data['__fcType'] = $item->getType();
                                $data['__fcFieldname'] = $fieldname;
                                $data['__itemIdx'] = $idx;

                                $data['id'] = $value['id'];
                                $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();
                                $fieldHelper->extractData($data, $item, $args, $context, $resolveInfo);
                                $lofItems[] = $data;
                            }
                        }

                        return $lofItems;
                    }

                    return null;
                }

            ];
    }

    /**
     * @return FieldDefinition[]
     *
     * @throws InvariantViolation
     */
    public function getFields(): array
    {
        if (null === $this->fields) {
            $fields = isset($this->config['fields']) ? $this->config['fields'] : [];
            $this->fields = FieldDefinition::defineFieldMap($this, $fields);
        }

        return $this->fields;
    }
}
