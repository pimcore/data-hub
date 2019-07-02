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
declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\AssetFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DataObjectFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator\Factory\OperatorFactoryInterface;
use Pimcore\Bundle\DataHubBundle\GraphQL\Query\Value\DefaultValue;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Cache\Runtime;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;
use Pimcore\Model\DataObject\Objectbrick\Definition;
use Pimcore\Model\Factory;
use Pimcore\Translation\Translator;
use Psr\Container\ContainerInterface;

class Service
{
    /***
     * @var ContainerInterface
     */
    protected $queryTypeGeneratorFactories;

    /***
     * @var ContainerInterface
     */
    protected $mutationTypeGeneratorFactories;

    /**
     * @var ContainerInterface
     */
    protected $queryOperatorFactories;

    /**
     * @var ContainerInterface
     */
    protected $mutationOperatorFactories;

    /**
     * @var array
     */
    protected $supportedQueryDataTypes;

    /**
     * @var array
     */
    protected $supportedMutationDataTypes;

    /**
     * @var DataObjectFieldHelper
     */
    protected $objectFieldHelper;

    /**
     * @var AssetFieldHelper
     */
    protected $assetFieldHelper;

    /**
     * @var LocaleServiceInterface
     */
    protected $localeService;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var Factory
     */
    protected $modelFactory;

    /**
     * @var array
     */
    protected $dataTypes = [];

    /**
     * Service constructor.
     * @param AssetFieldHelper $assetFieldHelper
     * @param DataObjectFieldHelper $objectFieldHelper
     * @param LocaleServiceInterface $localeService
     * @param Factory $modelFactory
     * @param Translator $translator
     * @param ContainerInterface $queryTypeGeneratorFactories
     * @param ContainerInterface $queryOperatorFactories
     * @param ContainerInterface $mutationTypeGeneratorFactories
     * @param ContainerInterface $mutationOperatorFactories
     */
    public function __construct(
        AssetFieldHelper $assetFieldHelper,
        DataObjectFieldHelper $objectFieldHelper,
        LocaleServiceInterface $localeService,
        Factory $modelFactory,
        Translator $translator,
        ContainerInterface $queryTypeGeneratorFactories,
        ContainerInterface $queryOperatorFactories,
        ContainerInterface $mutationTypeGeneratorFactories,
        ContainerInterface $mutationOperatorFactories
    )
    {
        $this->assetFieldHelper = $assetFieldHelper;
        $this->objectFieldHelper = $objectFieldHelper;
        $this->localeService = $localeService;
        $this->modelFactory = $modelFactory;
        $this->translator = $translator;
        $this->queryTypeGeneratorFactories = $queryTypeGeneratorFactories;
        $this->queryOperatorFactories = $queryOperatorFactories;
        $this->mutationTypeGeneratorFactories = $mutationTypeGeneratorFactories;
        $this->mutationOperatorFactories = $mutationOperatorFactories;
    }

    /**
     * @param $attribute
     * @param $typeName
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|\Pimcore\Model\DataObject\Fieldcollection\Definition|null $class
     * @param null $container
     *
     * @return mixed
     */
    public function buildQueryDataConfig($attribute, $typeName, $fieldDefinition = null, $class = null, $container = null)
    {
        /** @var QueryFieldConfigGeneratorInterface $factory */
        $factory = $this->queryTypeGeneratorFactories->get('typegenerator_querydatatype_' . $typeName);
        $result = $factory->getGraphQlFieldConfig($attribute, $fieldDefinition, $class, $container);
        return $result;
    }


    /**
     * @param $nodeDef
     * @param $typeName
     * @param ClassDefinition|null $class
     * @param null $container
     * @return mixed
     */
    public function buildMutationDataConfig($nodeDef, ClassDefinition $class = null, $container = null)
    {
        /** @var MutationFieldConfigGeneratorInterface $factory */
        $typeName = $nodeDef["attributes"]["dataType"];
        $factory = $this->mutationTypeGeneratorFactories->get('typegenerator_mutationdatatype_' . $typeName);
        $result = $factory->getGraphQlMutationFieldConfig($nodeDef, $class, $container);
        return $result;
    }

    /**
     * @param $name
     * @param $attribute
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|null $class
     * @param null $container
     *
     * @return mixed
     */
    public function buildDataQueryResolver($attribute, Data $fieldDefinition = null, ClassDefinition $class = null)
    {
        $name = $fieldDefinition->getFieldtype();
        /** @var QueryFieldConfigGeneratorInterface $factory */
        $factory = $this->queryTypeGeneratorFactories->get('typegenerator_querydatatype_' . $name);
        $resolver = $factory->getResolver($attribute, $fieldDefinition, $class);

        return $resolver;
    }

    /**
     * @param $name
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|null $class
     * @param null $container
     *
     * @return mixed
     */
    public function buildDataQueryType(Data $fieldDefinition = null, ClassDefinition $class = null, $container = null)
    {
        $name = $fieldDefinition->getFieldtype();
        /** @var QueryFieldConfigGeneratorInterface $factory */
        $factory = $this->queryTypeGeneratorFactories->get('typegenerator_querydatatype_' . $name);
        $result = $factory->getFieldType($fieldDefinition, $class, $container);

        return $result;
    }

    /**
     * @param $typeName
     *
     * @return bool
     */
    public function supportsQueryDataType($typeName)
    {
        return $this->queryTypeGeneratorFactories->has('typegenerator_querydatatype_' . $typeName);
    }

    /**
     * @param $typeName
     *
     * @return bool
     */
    public function supportsMutationDataType($typeName)
    {
        return $this->mutationTypeGeneratorFactories->has('typegenerator_mutationdatatype_' . $typeName);
    }


    /**
     * @param $typeName
     * @param $nodeDef
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|null $class
     * @param null $container
     *
     * @return mixed
     */
    public function buildQueryOperatorConfig($typeName, $nodeDef, ClassDefinition $class = null, $container = null, $params = [])
    {
        $typeName = strtolower($typeName);
        /** @var QueryFieldConfigGeneratorInterface $factory */
        $factory = $this->queryTypeGeneratorFactories->get('typegenerator_queryoperator_' . $typeName);
        $result = $factory->getGraphQlQueryOperatorConfig($typeName, $nodeDef, $class, $container, $params);
        return $result;
    }

    /**
     * @param $typeName
     * @param $nodeDef
     * @param ClassDefinition|null $class
     * @param null $container
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function buildMutationOperatorConfig($typeName, $nodeDef, ClassDefinition $class = null, $container = null, $params = [])
    {
        $typeName = strtolower($typeName);

        // $factory = $this->mutationTypeGeneratorFactories->get('typegenerator_mutationoperator_' . $typeName);
        $factory = $this->mutationOperatorFactories->get($typeName);
        $context = Runtime::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        $configGenerator = $factory->build($nodeDef["attributes"], $context);
        $result = $configGenerator->getGraphQlMutationOperatorConfig($nodeDef, $class, $container, $params);
        return $result;
    }

    /**
     * @param $mode
     * @param $typeName
     * @param $nodeDef
     * @param ClassDefinition|null $class
     * @param null $container
     * @param array $params
     * @return mixed
     */
    public function buildOperatorQueryType($mode, $typeName, $nodeDef, ClassDefinition $class = null, $container = null, $params = [])
    {
        $typeName = strtolower($typeName);
        /** @var QueryFieldConfigGeneratorInterface $factory */
        $factory = $this->queryTypeGeneratorFactories->get('typegenerator_operator_' . $typeName);
        $result = $factory->getGraphQlOperatorConfig($mode, $typeName, $nodeDef, $class, $container, $params);

        return $result;
    }

    /**
     * @param $typeName
     * @param $attributes
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|null $class
     * @param null $container
     *
     * @return mixed
     */
    public function buildQueryOperator($typeName, $attributes = null, ClassDefinition $class = null, $container = null)
    {
        $typeName = strtolower($typeName);
        /** @var OperatorFactoryInterface $factory */
        $factory = $this->queryOperatorFactories->get($typeName);

        $context = Runtime::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        $result = $factory->build($attributes, $context);

        return $result;
    }

    /**
     * @param $nodeConfig
     * @return mixed|DefaultValue
     * @throws \Exception
     */
    public function buildValueResolverFromAttributes($nodeConfig)
    {
        $attributes = $nodeConfig['attributes'];
        if ($nodeConfig['isOperator']) {
            $class = $attributes['class'];
            $operatorImpl = $this->buildQueryOperator($class, $attributes);

            return $operatorImpl;
        } else {
            $context = Runtime::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
            $operatorImpl = new DefaultValue($attributes, $context);
            $operatorImpl->setGraphQlService($this);

            return $operatorImpl;
        }
    }

    /**
     * @return array
     */
    public function getSupportedQueryDataTypes()
    {
        return $this->supportedQueryDataTypes;
    }

    /**
     * @param $supportedQueryDataTypes
     */
    public function setSupportedQueryDataTypes($supportedQueryDataTypes)
    {
        $this->supportedQueryDataTypes = $supportedQueryDataTypes;
    }

    /**
     * @return array
     */
    public function getSupportedMutationDataTypes(): array
    {
        return $this->supportedMutationDataTypes;
    }

    /**
     * @param array $supportedMutationDataTypes
     */
    public function setSupportedMutationDataTypes(array $supportedMutationDataTypes): void
    {
        $this->supportedMutationDataTypes = $supportedMutationDataTypes;
    }


    /**
     * @param $value
     * @return mixed
     */
    public function getElementFromArrayObject($value)
    {
        if ($value instanceof \ArrayObject) {
            $value = $value->getArrayCopy();
            if (isset($value["__elementType"])) {
                $value = \Pimcore\Model\Element\Service::getElementById($value["__elementType"], $value["id"]);
            }
        }

        return $value;

    }

    /**
     * @return AssetFieldHelper
     */
    public function getAssetFieldHelper()
    {
        return $this->assetFieldHelper;
    }

    /**
     * @return DataObjectFieldHelper
     */
    public function getObjectFieldHelper()
    {
        return $this->objectFieldHelper;
    }

    /**
     * @return ContainerInterface
     */
    public function getQueryTypeGeneratorFactories(): ContainerInterface
    {
        return $this->queryTypeGeneratorFactories;
    }

    /**
     * @return ContainerInterface
     */
    public function getQueryOperatorFactories(): ContainerInterface
    {
        return $this->queryOperatorFactories;
    }

    /**
     * @return LocaleServiceInterface
     */
    public function getLocaleService(): LocaleServiceInterface
    {
        return $this->localeService;
    }

    /**
     * @return Factory
     */
    public function getModelFactory(): Factory
    {
        return $this->modelFactory;
    }

    /**
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param $dataTypes
     */
    public function registerDataTypes($dataTypes)
    {
        $this->dataTypes = $dataTypes;
    }

    /**
     * @param $typename
     * @return mixed
     * @throws \Exception
     */
    public function getTypeDefinition($typename)
    {
        if (isset($this->dataTypes[$typename])) {
            return $this->dataTypes[$typename];
        }
        throw new \Exception("unknown type: " . $typename);
    }

    /**
     * gets value for given object and getter, including inherited values
     *
     * @static
     *
     * @param $object
     * @param $key
     * @param null $brickType
     * @param null $brickKey
     * @param null $fieldDefinition
     *
     * @return \stdclass, value and objectid where the value comes from
     */
    public static function getValueForObject($object, $key, $brickType = null, $brickKey = null, $fieldDefinition = null, $context = [], $brickDescriptor = null, $args = [])
    {

        //TODO once this gets integrated into the core, share the code with Service::getValueFromObject
        $getter = 'get' . ucfirst($key);
        $value = $object->$getter();
        if (!empty($value) && !empty($brickType)) {
            $getBrickType = 'get' . ucfirst($brickType);
            $value = $value->$getBrickType();
            if (!empty($value) && !empty($brickKey)) {
                if ($brickDescriptor) {
                    $innerContainer = $brickDescriptor['innerContainer'] ? $brickDescriptor['innerContainer'] : 'localizedfields';
                    $localizedFields = $value->{'get' . ucfirst($innerContainer)}();
                    $brickDefinition = Definition::getByKey($brickType);
                    $fieldDefinitionLocalizedFields = $brickDefinition->getFieldDefinition('localizedfields');
                    $fieldDefinition = $fieldDefinitionLocalizedFields->getFieldDefinition($brickKey);
                    $value = $localizedFields->getLocalizedValue($brickDescriptor['brickfield'], isset($args["language"]) ? $args["language"] : null);
                } else {
                    $brickFieldGetter = 'get' . ucfirst($brickKey);
                    $value = $value->$brickFieldGetter();
                }
            }
        }

        if (!$fieldDefinition) {
            $fieldDefinition = $object->getClass()->getFieldDefinition($key, $context);
        }

        if (!empty($brickType) && !empty($brickKey) && !$brickDescriptor) {
            $brickClass = Definition::getByKey($brickType);
            $context = ['object' => $object, 'outerFieldname' => $key];
            $fieldDefinition = $brickClass->getFieldDefinition($brickKey, $context);
        }

        if ($fieldDefinition->isEmpty($value)) {
            $parent = \Pimcore\Model\DataObject\Service::hasInheritableParentObject($object);
            if (!empty($parent)) {
                return self::getValueForObject($parent, $key, $brickType, $brickKey, $fieldDefinition, $context, $brickDescriptor);
            }
        }

        return $value;
    }

    /**
     * @param $object
     * @param Data $fieldDefinition
     * @param $attribute
     * @param array $args
     * @return \stdclass|null
     * @throws \Exception
     */
    public static function setValue($object, /* Data $fieldDefinition, */ $attribute, $callback)
    {

        $setter = $attribute ? 'set' . ucfirst($attribute) : $attribute;

        if (!$object) {
            return null;
        }
        $container = $object;

        /** @var Concrete $element */
        $attributeParts = explode('~', $attribute);

        $brickType = null;
        $brickKey = null;

        if (substr($attribute, 0, 1) == '~') {
            // key value, ignore for now
        } elseif (count($attributeParts) > 1) {
            // TODO once the datahub gets integrated into the core we should try to share this code
            // with Pimcore\Model\DataObject\Service::gridObjectData
            $context = ["object" => $object];

            // brick
            $brickType = $attributeParts[0];
            if (strpos($brickType, '?') !== false) {
                $brickDescriptor = substr($brickType, 1);
                $brickDescriptor = json_decode($brickDescriptor, true);
                $brickType = $brickDescriptor['containerKey'];
            }

            $brickKey = $attributeParts[1];
            $key = \Pimcore\Model\DataObject\Service::getFieldForBrickType($object->getclass(), $brickType);

            $brickClass = Definition::getByKey($brickType);
            $context['outerFieldname'] = $key;

            if ($brickDescriptor) {
                $def = $brickClass->getFieldDefinition($brickKey);
                if (!$def) {
                    $innerContainer = $brickDescriptor['innerContainer'] ? $brickDescriptor['innerContainer'] : 'localizedfields';
                    $localizedFields = $brickClass->getFieldDefinition($innerContainer);
                    $def = $localizedFields->getFieldDefinition($brickDescriptor['brickfield']);
                }
            } else {
                $def = $brickClass->getFieldDefinition($brickKey, $context);
            }

            if (!empty($key)) {
                // if the definition is not set try to get the definition from localized fields
                if (!$def) {
                    if ($locFields = $object->getClass()->getFieldDefinition('localizedfields')) {
                        $def = $locFields->getFieldDefinition($key, $context);
                    }
                }
                $brickGetter = "get" . ucfirst($key);

                $brickContainer = $object->$brickGetter();
                $subBrickGetter = "get" . ucfirst($brickType);
                $subBrickSetter = "set" . ucfirst($brickType);
                $subBrickType = $brickContainer->$subBrickGetter();

                if (!$subBrickType) {
                    /** @var  $brickClass AbstractData */
                    $brickClass = 'Pimcore\\Model\\DataObject\\Objectbrick\\Data\\' . ucfirst($brickType);
                    $subBrickType = new $brickClass($object);
                    $subBrickSetter = "set" . ucfirst($brickType);
                    $brickContainer->$subBrickSetter($subBrickType);
                }

                $innerSetter = "set" . ucfirst($def->getName());
                $result = $callback($subBrickType, $innerSetter);

                $brickContainer->$subBrickSetter($subBrickType);


                return $result;

            }

        } else if (method_exists($container, $setter)) {
            $result = $callback($container, $setter);
        }
        return $result;
    }

    /**
     * @param $objectId
     * @param Data $fieldDefinition
     * @param $attribute
     * @param array $args
     * @param null $descriptor
     * @return \stdclass|null
     */
    public static function resolveValue($objectId, Data $fieldDefinition, $attribute, $args = [], $descriptor = null)
    {
        $getter = 'get' . ucfirst($fieldDefinition->getName());
        $object = Concrete::getById($objectId);
        if (!$object) {
            return null;
        }
        $result = null;
        $container = $object;

        /** @var Concrete $element */
        $attributeParts = explode('~', $attribute);

        $brickType = null;
        $brickKey = null;

        if ($descriptor instanceof FieldcollectionDecriptor) {

            $descriptorData = $descriptor->getArrayCopy();
            $fcFieldNameGetter = "get" . ucfirst($descriptorData['__fcFieldname']);
            $fcData = $object->$fcFieldNameGetter();
            if ($fcData) {
                $items = $fcData->getItems();
                $idx = $descriptorData["__itemIdx"];
                $itemData = $items[$idx];
                if (isset($args) && isset($args["language"])) {
                    $result = $itemData->$getter($args["language"]);
                } else {
                    $result = $itemData->$getter();
                }
            }
        } else if (substr($attribute, 0, 1) == '~') {
            // key value, ignore for now
        } elseif (count($attributeParts) > 1) {
            // TODO once the datahub gets integrated into the core we should try to share this code
            // with Pimcore\Model\DataObject\Service::gridObjectData
            $context = ["object" => $object];

            // brick
            $brickType = $attributeParts[0];
            if (strpos($brickType, '?') !== false) {
                $brickDescriptor = substr($brickType, 1);
                $brickDescriptor = json_decode($brickDescriptor, true);
                $brickType = $brickDescriptor['containerKey'];
            }

            $brickKey = $attributeParts[1];
            $key = \Pimcore\Model\DataObject\Service::getFieldForBrickType($object->getclass(), $brickType);

            $brickClass = Definition::getByKey($brickType);
            $context['outerFieldname'] = $key;

            if ($brickDescriptor) {
                $innerContainer = $brickDescriptor['innerContainer'] ? $brickDescriptor['innerContainer'] : 'localizedfields';
                $localizedFields = $brickClass->getFieldDefinition($innerContainer);
                $def = $localizedFields->getFieldDefinition($brickDescriptor['brickfield']);
            } else {
                $def = $brickClass->getFieldDefinition($brickKey, $context);
            }

            if (!empty($key)) {
                // if the definition is not set try to get the definition from localized fields
                if (!$def) {
                    if ($locFields = $object->getClass()->getFieldDefinition('localizedfields')) {
                        $def = $locFields->getFieldDefinition($key, $context);
                    }
                }
                $value = Service::getValueForObject($object, $key, $brickType, $brickKey, $def, $context, $brickDescriptor, $args);
                return $value;

            }

        } else if (method_exists($container, $getter)) {
            $result = $container->$getter();
        }
        return $result;
    }

    /**
     * @return ContainerInterface
     */
    public function getMutationTypeGeneratorFactories(): ContainerInterface
    {
        return $this->mutationTypeGeneratorFactories;
    }

    /**
     * @param ContainerInterface $mutationTypeGeneratorFactories
     */
    public function setMutationTypeGeneratorFactories(ContainerInterface $mutationTypeGeneratorFactories): void
    {
        $this->mutationTypeGeneratorFactories = $mutationTypeGeneratorFactories;
    }

    /**
     * @return ContainerInterface
     */
    public function getMutationOperatorFactories(): ContainerInterface
    {
        return $this->mutationOperatorFactories;
    }

    /**
     * @param ContainerInterface $mutationOperatorFactories
     */
    public function setMutationOperatorFactories(ContainerInterface $mutationOperatorFactories): void
    {
        $this->mutationOperatorFactories = $mutationOperatorFactories;
    }

    /**
     * @return array
     */
    public function getDataTypes(): array
    {
        return $this->dataTypes;
    }

    /**
     * @param array $dataTypes
     */
    public function setDataTypes(array $dataTypes): void
    {
        $this->dataTypes = $dataTypes;
    }




}
