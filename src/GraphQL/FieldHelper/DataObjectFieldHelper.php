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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper;

use GraphQL\Language\AST\FieldNode;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\File;
use Pimcore\Logger;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Pimcore\Model\DataObject\Localizedfield;
use Pimcore\Model\DataObject\Objectbrick\Definition;

class DataObjectFieldHelper extends AbstractFieldHelper
{
    /**
     * @param array $nodeDef
     * @param ClassDefinition|\Pimcore\Model\DataObject\Fieldcollection\Definition $class
     * @param object|null $container
     *
     * @return array|bool|null
     */
    public function getQueryFieldConfigFromConfig($nodeDef, $class, $container = null)
    {
        $result = false;

        $attributes = $nodeDef['attributes'];

        if ($nodeDef['isOperator']) {
            $key = isset($attributes['label']) ? $attributes['label'] : '';

            $key = File::getValidFilename($key);

            $result = [
                'key' => $key,
                'config' => $this->getGraphQlOperatorConfig(
                    'query',
                    $nodeDef,
                    $class,
                    null,
                    []
                )];
        } else {
            $key = $attributes['attribute'];

            // system columns which are not part of the common set (see PimcoreObjectType)
            if ($attributes['dataType'] == 'system') {
                switch ($key) {
                    case 'creationDate':
                    case 'modificationDate':
                        return [
                            'key' => $key,
                            'config' => [
                                'name' => $key,
                                'type' => Type::int()
                            ]
                        ];
                    case 'filename':
                    case 'fullpath':
                    case 'key':
                        return [
                            'key' => $key,
                            'config' => [
                                'name' => $key,
                                'type' => Type::string()
                            ]
                        ];
                    case 'published':
                        return [
                            'key' => $key,
                            'config' => [
                                'name' => $key,
                                'type' => Type::boolean(),
                            ]
                        ];
                    default:
                        return null;
                }
            } else {
                $fieldDefinition = $this->getFieldDefinitionFromKey($class, $key, $container);

                if (!$fieldDefinition) {
                    Logger::error('could not resolve field "' . $key . '" in class ' . $class->getName());

                    return false;
                }

                if ($this->supportsGraphQL($fieldDefinition, 'query')) {
                    $fieldName = $fieldDefinition->getName();

                    $result = ['key' => $fieldName,
                        'config' => $this->getGraphQlQueryFieldConfig(
                            $key,
                            $fieldDefinition,
                            $class,
                            $container
                        )];
                }
            }
        }

        return $result;
    }

    /**
     * @param string $mode
     * @param array $nodeDef
     * @param ClassDefinition $class
     * @param object|null $container
     * @param array $params
     *
     * @return mixed
     */
    public function getGraphQlOperatorConfig($mode, $nodeDef, $class, $container, $params = [])
    {
        $attributes = $nodeDef['attributes'];
        $operatorTypeName = $attributes['class'];

        $builder = 'buildDataObject' . ucfirst($mode) . 'OperatorConfig';
        $typeDef = $this->getGraphQlService()->$builder($operatorTypeName, $nodeDef, $class, $container, $params);

        return $typeDef;
    }

    /**
     * @param ClassDefinition|\Pimcore\Model\DataObject\Fieldcollection\Definition $class
     * @param string $key
     * @param object|null $container
     *
     * @return Data|null
     */
    public function getFieldDefinitionFromKey($class, $key, &$container = null)
    {
        $fieldDefinition = null;
        $parts = explode('~', $key);

        if (substr($key, 0, 1) === '~') {
            // classification store ...
        } elseif (count($parts) > 1) {
            $brickType = $parts[0];
            $brickDescriptor = null;

            if (strpos($brickType, '?') !== false) {
                $brickDescriptor = substr($brickType, 1);
                $brickDescriptor = json_decode($brickDescriptor, true);
                $brickType = $brickDescriptor['containerKey'];
            }

            $brickKey = $parts[1];

            $brickDefinition = Definition::getByKey($brickType);

            if ($brickDescriptor) {
                /** @var Data\Localizedfields|null $fieldDefinitionLocalizedFields */
                $fieldDefinitionLocalizedFields = $brickDefinition->getFieldDefinition('localizedfields');
                if ($fieldDefinition = $fieldDefinitionLocalizedFields?->getFieldDefinition($brickKey)) {
                    $container = $fieldDefinitionLocalizedFields;
                }
            }

            if (!$fieldDefinition) {
                $fieldDefinition = $brickDefinition->getFieldDefinition($brickKey);
            }
        } else {
            /** @var Data\Localizedfields|null $fieldDefinitionLocalizedFields */
            $fieldDefinitionLocalizedFields = $class->getFieldDefinition('localizedfields');
            if ($fieldDefinition = $fieldDefinitionLocalizedFields?->getFieldDefinition($key)) {
                $container = $fieldDefinitionLocalizedFields;
            }
            if (!$fieldDefinition) {
                $fieldDefinition = $class->getFieldDefinition($key);
            }
        }

        return $fieldDefinition;
    }

    /**
     * @param Data $fieldDefinition
     * @param string $operationType
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function supportsGraphQL(Data $fieldDefinition, string $operationType)
    {
        $typeName = $fieldDefinition->getFieldtype();

        switch ($operationType) {
            case 'query':
                return $this->getGraphQlService()->supportsDataObjectQueryDataType($typeName);
            case 'mutation':
                return $this->getGraphQlService()->supportsDataObjectMutationDataType($typeName);
            default:
                throw new ClientSafeException('unknown operation type ' . $typeName);
        }
    }

    /**
     * @param string $attribute
     * @param Data $fieldDefinition
     * @param ClassDefinition $class
     * @param object $container
     *
     * @return mixed
     */
    public function getGraphQlQueryFieldConfig($attribute, $fieldDefinition, $class, $container)
    {
        $typeName = $fieldDefinition->getFieldtype();
        $typeDef = $this->getGraphQlService()->buildDataObjectQueryDataConfig($attribute, $typeName, $fieldDefinition, $class, $container);

        return $typeDef;
    }

    /**
     * @param array $nodeDef
     * @param ClassDefinition|\Pimcore\Model\DataObject\Fieldcollection\Definition $class
     *
     * @return array|false|null
     */
    public function getMutationFieldConfigFromConfig($nodeDef, $class)
    {
        $container = null;
        $result = false;

        $attributes = $nodeDef['attributes'];

        if ($nodeDef['isOperator'] ?? false) {
            $key = isset($attributes['label']) ? $attributes['label'] : '';
            $key = preg_replace('/[^A-Za-z0-9\-\.~_]+/', '_', $key);

            $result = $this->getGraphQlOperatorConfig(
                'mutation',
                $nodeDef,
                $class,
                null,
                []
            );

            $result['key'] = $key;
        } else {
            $key = $attributes['attribute'];

            // system columns which are not part of the common set (see PimcoreObjectType)
            if ($attributes['dataType'] === 'system') {
                switch ($key) {
                    case 'key':
                        return [
                            'key' => $key,
                            'arg' => ['type' => Type::string()],
                            'processor' => function ($object, $newValue, $args) {
                                $object->setKey($newValue);
                            }

                        ];
                    case 'published':
                        return [
                            'key' => $key,
                            'arg' => ['type' => Type::boolean()],
                            'processor' => function ($object, $newValue, $args) {
                                $object->setPublished($newValue);
                            }
                        ];
                    default:
                        return null;
                }
            } else {
                $fieldDefinition = $this->getFieldDefinitionFromKey($class, $key, $container);

                if (!$fieldDefinition) {
                    Logger::error('could not resolve field ' . $key);

                    return false;
                }

                if ($this->supportsGraphQL($fieldDefinition, 'mutation')) {
                    $fieldName = $fieldDefinition->getName();

                    $result = $this->getGraphQlMutationFieldConfig(
                        $nodeDef,
                        $class,
                        $container
                    );
                    $result['key'] = $fieldName;
                }
            }
        }

        return $result;
    }

    /**
     * @param array $nodeDef
     * @param ClassDefinition|\Pimcore\Model\DataObject\Fieldcollection\Definition $class
     * @param object $container
     *
     * @return array
     */
    public function getGraphQlMutationFieldConfig($nodeDef, $class, $container)
    {
        $typeDef = $this->getGraphQlService()->buildDataObjectMutationDataConfig($nodeDef, $class, $container);

        return $typeDef;
    }

    /**
     * @param array $nodeConf
     * @param ClassDefinition $class
     * @param object|null $container
     *
     * @return mixed
     */
    public function getGraphQlTypeFromNodeConf($nodeConf, $class, $container = null)
    {
        $attributes = $nodeConf['attributes'];

        if ($nodeConf['isOperator']) {
            $operatorTypeName = $attributes['class'];
            $type = $this->getGraphQlService()->buildDataObjectOperatorQueryType('mutation', $operatorTypeName, $nodeConf, $class, $container);
        } else {
            $key = $attributes['attribute'];
            $fieldDefinition = $this->getFieldDefinitionFromKey($class, $key);
            $type = $this->getGraphQlService()->buildDataObjectDataQueryType($fieldDefinition, $class, $container);
        }

        return $type;
    }

    /**
     * @param FieldNode $ast
     * @param array $data
     * @param object $container
     * @param array $args
     * @param array $context
     * @param ResolveInfo $resolveInfo
     */
    public function doExtractData(FieldNode $ast, &$data, $container, $args, $context, $resolveInfo = null)
    {
        $astName = $ast->name->value;

        // sometimes we just want to expand relations just to throw them away afterwards because not requested
        if ($this->skipField($container, $astName)) {
            return;
        }

        // example for http://webonyx.github.io/graphql-php/error-handling/
//         throw new MySafeException("fieldhelper", "TBD customized error message");

        $getter = 'get' . ucfirst($astName);

        $isLocalizedField = false;
        $containerDefinition = null;

        if ($container instanceof Concrete) {
            $containerDefinition = $container->getClass();
        } elseif ($container instanceof AbstractData || $container instanceof \Pimcore\Model\DataObject\Objectbrick\Data\AbstractData) {
            $containerDefinition = $container->getDefinition();
        }

        if ($containerDefinition) {
            /** @var Data\Localizedfields|null $lfDefs */
            $lfDefs = $containerDefinition->getFieldDefinition('localizedfields');
            if ($lfDefs && $lfDefs->getFieldDefinition($astName)) {
                $isLocalizedField = true;
            }
        }

        if (method_exists($container, $getter)) {
            if ($isLocalizedField) {
                // defer it
                $data[$astName] = function ($source, $args, $context, ResolveInfo $info) use (
                    $container,
                    $getter
                ) {
                    return $container->$getter($args['language'] ?? null);
                };
            } else {
                $data[$astName] = $container->$getter();
            }
        }
    }

    /**
     * @param object $container
     * @param string $astName
     *
     * @return bool
     */
    public function skipField($container, $astName)
    {
        if ($container instanceof Concrete || $container instanceof Localizedfield) {
            $fieldDefinition = $container->getClass()->getFieldDefinition($astName);

            if ($fieldDefinition instanceof Data\Relations\AbstractRelations) {
                // do not autoexpand relations
                return true;
            }
        }

        return false;
    }
}
