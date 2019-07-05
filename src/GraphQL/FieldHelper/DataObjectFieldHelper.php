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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\QueryFieldConfigGeneratorInterface;
use Pimcore\File;
use Pimcore\Logger;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Localizedfield;
use Pimcore\Model\DataObject\Objectbrick\Definition;

class DataObjectFieldHelper extends AbstractFieldHelper
{
    /**
     * @param $container
     * @param $astName
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
    }

    /**
     * @param $attribute
     * @param Data|string $fieldDefinition
     * @param $class
     * @param $container
     *
     * @return mixed
     */
    public function getGraphQlQueryFieldConfig($attribute, $fieldDefinition, $class, $container)
    {
        $typeName = $fieldDefinition->getFieldtype();
        $typeDef = $this->getGraphQlService()->buildQueryDataConfig($attribute, $typeName, $fieldDefinition, $class, $container);
        return $typeDef;
    }


    /**
     * @param $nodeDef
     * @param $class
     * @param $container
     * @return mixed
     */
    public function getGraphQlMutationFieldConfig($nodeDef, $class, $container)
    {
        $typeDef = $this->getGraphQlService()->buildMutationDataConfig($nodeDef, $class, $container);
        return $typeDef;
    }
    /**
     * @param mixed $nodeDef
     * @param $class
     * @param $container
     *
     * @return mixed
     */
    public function getGraphQlOperatorConfig($mode, $nodeDef, $class, $container, $params = [])
    {
        $attributes = $nodeDef['attributes'];
        $operatorTypeName = $attributes['class'];

        $builder = "build" . ucfirst($mode) . "OperatorConfig";
        $typeDef = $this->getGraphQlService()->$builder($operatorTypeName, $nodeDef, $class, $container, $params);

        return $typeDef;
    }

    /**
     * @param Data $fieldDefinition
     * @param string $operationType
     * @return bool
     * @throws \Exception
     */
    public function supportsGraphQL(Data $fieldDefinition, string $operationType)
    {
        $typeName = $fieldDefinition->getFieldtype();

        switch ($operationType) {
            case 'query':
                return $this->getGraphQlService()->supportsQueryDataType($typeName);
            case 'mutation':
                return $this->getGraphQlService()->supportsMutationDataType($typeName);
            default:
                throw new \Exception("unknown operation type");
        }
    }

    /**
     * @param $nodeDef
     * @param $class
     * @return array|bool|null
     * @throws \Exception
     */
    public function getQueryFieldConfigFromConfig($nodeDef, $class)
    {
        $container = null;
        $result = false;

        $attributes = $nodeDef['attributes'];

        if ($nodeDef['isOperator']) {
            $key = $attributes['label'];

            $key = File::getValidFilename($key);

            $result = [
                'key' => $key,
                'config' => $this->getGraphQlOperatorConfig(
                    "query",
                    $nodeDef,
                    $class,
                    null,
                    []
                )];
        } else {
            $key = $attributes['attribute'];

            // system columns which are not part of the common set (see PimcoreObjectType)
            if ($attributes['dataType'] === 'system') {
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
                    case 'parent':
                        return [
                            'key' => $key,
                            'config' => [
                                'name' => $key,
                                'type' => Type::id(),
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
                    Logger::error('could not resolve field ' . $key);

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
     * @param $nodeDef
     * @param $class
     * @param $inputFields
     * @return array|bool|mixed|null
     * @throws \Exception
     */
    public function getMutationFieldConfigFromConfig($nodeDef, $class, $inputFields)
    {
        $container = null;
        $result = false;

        $attributes = $nodeDef['attributes'];

        if ($nodeDef['isOperator']) {
            $key = $attributes['label'];
            $key = preg_replace('/[^A-Za-z0-9\-\.~_]+/', '_', $key);

            $result = $this->getGraphQlOperatorConfig(
                "mutation",
                $nodeDef,
                $class,
                null,
                []
            );

            $result["key"] = $key;
        } else {
            $key = $attributes['attribute'];


            // system columns which are not part of the common set (see PimcoreObjectType)
            if ($attributes['dataType'] === 'system') {
                switch ($key) {
                    case 'key':
                        return [
                            'key' => $key,
                            'arg' => ['type' => Type::string()],
                            'processor' => static function($object, $newValue) {
                                $object->setKey($newValue);
                            }
                        ];
                    case 'published':
                        return [
                            'key' => $key,
                            'arg' => ['type' => Type::boolean()],
                            'processor' => static function($object, $newValue) {
                                $object->setPublished($newValue);
                            }
                        ];
                    default:
                        return null;
                }
            } else {
                /** @var  $fieldDefinition */
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
     * @param $class
     * @param $key
     * @param null $container
     *
     * @return mixed
     */
    public function getFieldDefinitionFromKey($class, $key, &$container = null)
    {

        $fieldDefinition = null;
        $parts = explode('~', $key);

        if (substr($key, 0, 1) == '~') {
            // classification store ...
        } elseif (count($parts) > 1) {
            $brickType = $parts[0];

            if (strpos($brickType, '?') !== false) {
                $brickDescriptor = substr($brickType, 1);
                $brickDescriptor = json_decode($brickDescriptor, true);
                $brickType = $brickDescriptor['containerKey'];
            }

            $brickKey = $parts[1];

            $brickDefinition = Definition::getByKey($brickType);

            if ($brickDescriptor) {
                $fieldDefinition = $brickDefinition->getFieldDefinition($brickKey);
                if (!$fieldDefinition) {
                    $fieldDefinitionLocalizedFields = $brickDefinition->getFieldDefinition('localizedfields');
                    $container = $fieldDefinitionLocalizedFields;
                    $fieldDefinition = $fieldDefinitionLocalizedFields->getFieldDefinition($brickKey);
                }
            } else {
                $fieldDefinition = $brickDefinition->getFieldDefinition($brickKey);
            }
        } else {
            $fieldDefinition = $class->getFieldDefinition($key);
        }

        if (!$fieldDefinition) {
            $container = $class->getFieldDefinition('localizedfields');
            $lfDefs = $container;
            if ($lfDefs) {
                $fieldDefinition = $lfDefs->getFieldDefinition($key);
            }
        }

        return $fieldDefinition;
    }

    /**
     * @param $nodeConf
     * @param $class
     * @param null $container
     *
     * @return mixed
     */
    public function getGraphQlTypeFromNodeConf($nodeConf, $class, $container = null)
    {
        $attributes = $nodeConf['attributes'];

        if ($nodeConf['isOperator']) {
            $operatorTypeName = $attributes['class'];
            $type = $this->getGraphQlService()->buildOperatorQueryType($operatorTypeName, $nodeConf, $class, $container);
        } else {
            $key = $attributes['attribute'];
            $fieldDefinition = $this->getQueryFieldDefinitionFromKey($class, $key);
            /** @var QueryFieldConfigGeneratorInterface $factory */
            $type = $this->getGraphQlService()->buildDataQueryType($fieldDefinition, $class, $container);
        }

        return $type;
    }
}
