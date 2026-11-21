<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper;

use GraphQL\Language\AST\FieldNode;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Cache\RuntimeCache;
use Pimcore\File;
use Pimcore\Logger;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Pimcore\Model\DataObject\Localizedfield;
use Pimcore\Model\DataObject\Objectbrick\Definition;

final class DataObjectFieldHelper extends AbstractFieldHelper
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
                    case 'filename':
                    case 'fullpath':
                    case 'key':
                        return [
                            'key' => $key,
                            'config' => [
                                'name' => $key,
                                'type' => Type::string(),
                            ],
                        ];
                    case 'published':
                        return [
                            'key' => $key,
                            'config' => [
                                'name' => $key,
                                'type' => Type::boolean(),
                            ],
                        ];
                    default:
                        return null;
                }
            } else {
                $fieldDefinition = $this->getFieldDefinitionFromKey($class, $key, $container);

                if (!$fieldDefinition) {
                    // fields inside a block (also block -> localizedfields) are served by the block entry type
                    $blockName = $this->findBlockOfChildField($class, $key);
                    if ($blockName !== null) {
                        Logger::debug(
                            'skipping column "' . $key . '", it is a field of block "' . $blockName
                            . '" in class ' . $class->getName()
                        );
                    } else {
                        Logger::error('could not resolve field "' . $key . '" in class ' . $class->getName());
                    }

                    return false;
                }

                if ($this->supportsGraphQL($fieldDefinition, 'query')) {
                    $fieldName = $this->getBrickColumnName($key, $fieldDefinition->getName());

                    // the types of a column which selects its objectbricks field are named after its brick
                    $config = $this->getGraphQlQueryFieldConfig(
                        $key,
                        $fieldDefinition,
                        Service::getContainerSelectingBrick($key) ?? $class,
                        $container
                    );
                    $config['name'] = $fieldName;

                    if (isset($config['type'])) {
                        $config['type'] = $this->shareBrickColumnType($key, $fieldDefinition, $config['type']);
                    }

                    $result = ['key' => $fieldName, 'config' => $config];
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
     * The type of a brick column does not depend on the objectbricks field of the column (`Brick~field` and
     * `Brick~field~containerField`). The columns of the same brick field which produce a type of the same name get
     * the same instance, because a schema must not contain two instances of a type of that name.
     */
    private function shareBrickColumnType(string $attribute, Data $fieldDefinition, mixed $type): mixed
    {
        $brickType = Service::parseObjectBrickFieldName($attribute)['brickType'] ?? null;

        if ($brickType === null || !$type instanceof Type) {
            return $type;
        }

        $cacheKey = 'graphql_brick_column_type_' . $brickType . '_' . $fieldDefinition->getName()
            . '_' . Type::getNamedType($type)->toString();

        if (!RuntimeCache::isRegistered($cacheKey)) {
            RuntimeCache::set($cacheKey, $type);
        }

        return RuntimeCache::get($cacheKey);
    }

    /**
     * A brick column which selects its containing objectbricks field (`Brick~field~containerField`) is named
     * after the container too, so that the same brick of several fields can be used side by side.
     */
    private function getBrickColumnName(string $attribute, string $defaultName): string
    {
        $brickParts = Service::parseObjectBrickFieldName($attribute);

        if (empty($brickParts['containerField'])) {
            return $defaultName;
        }

        return $brickParts['containerField'] . '_' . $brickParts['brickType'] . '_' . $brickParts['brickKey'];
    }

    /**
     * @param ClassDefinition|\Pimcore\Model\DataObject\Fieldcollection\Definition $class
     *
     * @return string|null name of the block containing the field $key, null if there is none
     */
    private function findBlockOfChildField($class, string $key): ?string
    {
        foreach ($class->getFieldDefinitions(['suppressEnrichment' => true]) as $fieldDefinition) {
            if (!$fieldDefinition instanceof Data\Block) {
                continue;
            }

            foreach ($fieldDefinition->getFieldDefinitions(['suppressEnrichment' => true]) as $blockChild) {
                if ($blockChild->getName() === $key
                    || ($blockChild instanceof Data\Localizedfields
                        && $blockChild->getFieldDefinition($key, ['suppressEnrichment' => true]))
                ) {
                    return $fieldDefinition->getName();
                }
            }
        }

        return null;
    }

    /**
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
                            },

                        ];
                    case 'published':
                        return [
                            'key' => $key,
                            'arg' => ['type' => Type::boolean()],
                            'processor' => function ($object, $newValue, $args) {
                                $object->setPublished($newValue);
                            },
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
                    $fieldName = $this->getBrickColumnName($key, $fieldDefinition->getName());

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
                    $orgUseFallbackValues = Localizedfield::getGetFallbackValues();
                    Localizedfield::setGetFallbackValues(
                        $args['getFallbackLanguageValue'] ?? $orgUseFallbackValues
                    );
                    $localizedValue = $container->$getter($args['language'] ?? null);
                    Localizedfield::setGetFallbackValues($orgUseFallbackValues);

                    return $localizedValue;
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
