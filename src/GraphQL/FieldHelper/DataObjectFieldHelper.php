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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldConfigGeneratorInterface;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\File;
use Pimcore\Logger;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Localizedfield;

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
     * @param Data|string $fieldDefinition
     * @param $class
     * @param $container
     *
     * @return mixed
     */
    public function getGraphQlFieldConfig($fieldDefinition, $class, $container)
    {
        $typeName = $fieldDefinition->getFieldtype();

        /** @var $service Service */
        $service = \Pimcore::getContainer()->get(Service::class);
        $typeDef = $service->buildDataQueryConfig($typeName, $fieldDefinition, $class, $container);

        return $typeDef;
    }

    /**
     * @param mixed $nodeDef
     * @param $class
     * @param $container
     *
     * @return mixed
     */
    public function getGraphQlOperatorConfig($nodeDef, $class, $container, $params = [])
    {
        $attributes = $nodeDef['attributes'];
        $operatorTypeName = $attributes['class'];

        /** @var $service Service */
        $service = \Pimcore::getContainer()->get(Service::class);
        $typeDef = $service->buildOperatorQueryConfig($operatorTypeName, $nodeDef, $class, $container, $params);

        return $typeDef;
    }

    /**
     * @param Data $fieldDefinition
     *
     * @return bool
     */
    public function supportsGraphQL(Data $fieldDefinition)
    {
        $typeName = $fieldDefinition->getFieldtype();

        /** @var $service Service */
        $service = \Pimcore::getContainer()->get(Service::class);
        $isSupported = $service->supportsDataQueryType($typeName);

        return $isSupported;
    }

    /**
     * @param $nodeDef
     * @param $class
     * @param array $params
     * @param bool $ignoreSupports
     *
     * @return array|bool
     */
    public function getFieldConfigFromConfig($nodeDef, $class, $params = [], $ignoreSupports = false)
    {
        $container = null;
        $result = false;

        $attributes =  $nodeDef['attributes'];

        if ($nodeDef['isOperator']) {
            $key = $attributes['label'];

            $key = File::getValidFilename($key);

            $result = [
                'key' => $key,
                'config' => $this->getGraphQlOperatorConfig(
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
                    default: return null;
                }
            } else {
                $fieldDefinition = $this->getFieldDefinitionFromKey($class, $key, $container);

                if (!$fieldDefinition) {
                    Logger::error('could not resolve field ' . $key);

                    return false;
                }

                if ($this->supportsGraphQL($fieldDefinition)) {
                    $key = $fieldDefinition->getName();

                    $result = ['key' => $key,
                        'config' => $this->getGraphQlFieldConfig(
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
     * @param $class
     * @param $key
     * @param null $container
     *
     * @return mixed
     */
    public function getFieldDefinitionFromKey($class, $key, &$container = null)
    {
        $fieldDefinition = $class->getFieldDefinition($key);
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
        /** @var $service Service */
        $service = \Pimcore::getContainer()->get(Service::class);

        if ($nodeConf['isOperator']) {
            $operatorTypeName = $attributes['class'];
            $type = $service->buildOperatorQueryType($operatorTypeName, $nodeConf, $class, $container);
        } else {
            $key = $attributes['attribute'];
            $fieldDefinition = $this->getFieldDefinitionFromKey($class, $key);
            /** @var FieldConfigGeneratorInterface $factory */
            $type = $service->buildDataQueryType($fieldDefinition, $class, $container);
        }

        return $type;
    }
}
