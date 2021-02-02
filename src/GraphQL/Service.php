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

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\AssetFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DataObjectFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DocumentFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator\Factory\OperatorFactoryInterface;
use Pimcore\Bundle\DataHubBundle\GraphQL\Query\Value\DefaultValue;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Cache\Runtime;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;
use Pimcore\Model\DataObject\Objectbrick\Definition;
use Pimcore\Model\Document;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Factory;
use Pimcore\Translation\Translator;
use Psr\Container\ContainerInterface;

class Service
{
    /***
     * @var ContainerInterface
     */
    protected $dataObjectQueryTypeGeneratorFactories;

    /***
     * @var ContainerInterface
     */
    protected $dataObjectMutationTypeGeneratorFactories;

    /**
     * @var ContainerInterface
     */
    protected $dataObjectQueryOperatorFactories;

    /**
     * @var ContainerInterface
     */
    protected $documentElementQueryTypeGeneratorFactories;

    /**
     * @var ContainerInterface
     */
    protected $documentElementMutationTypeGeneratorFactories;


    /**
     * @var ContainerInterface
     */
    protected $dataObjectMutationOperatorFactories;

    /**
     * @var ContainerInterface
     */
    protected $generalTypeGeneratorFactories;

    /**
     * @var ContainerInterface
     */
    protected $assetTypeGeneratorFactories;

    /**
     * @var ContainerInterface
     */
    protected $csFeatureTypeGeneratorFactories;

    /**
     * @var array
     */
    protected $supportedDataObjectQueryDataTypes;

    /**
     * @var array
     */
    protected $supportedDocumentElementQueryDataTypes;

    /**
     * @var array
     */
    protected $supportedDocumentElementMutationDataTypes;

    /**
     * @var array
     */
    protected $supportedCsFeatureQueryDataTypes;

    /**
     * @var array
     */
    protected $supportedDataObjectMutationDataTypes;

    /**
     * @var DataObjectFieldHelper
     */
    protected $objectFieldHelper;

    /**
     * @var AssetFieldHelper
     */
    protected $assetFieldHelper;

    /**
     * @var documentFieldHelper
     */
    protected $documentFieldHelper;

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
    protected $generalTypes = [];

    /**
     * @var array
     */
    protected $assetDataTypes = [];

    /**
     * @var array
     */
    protected $documentDataTypes = [];

    /**
     * @var array
     */
    protected $propertyDataTypes = [];

    /**
     * @var array
     */
    protected $classificationStoreDataTypes = [];

    /**
     * @var array
     */
    protected $dataObjectDataTypes = [];


    protected $tagManagerListener;


    /**
     * Service constructor.
     * @param AssetFieldHelper $assetFieldHelper
     * @param DocumentFieldHelper $documentFieldHelper
     * @param DataObjectFieldHelper $objectFieldHelper
     * @param LocaleServiceInterface $localeService
     * @param Factory $modelFactory
     * @param Translator $translator
     * @param ContainerInterface $dataObjectQueryTypeGeneratorFactories
     * @param ContainerInterface $dataObjectQueryOperatorFactories
     * @param ContainerInterface $dataObjectMutationTypeGeneratorFactories
     * @param ContainerInterface $dataObjectMutationOperatorFactories
     * @param ContainerInterface $documentElementQueryTypeGeneratorFactories
     * @param ContainerInterface $documentElementMutationTypeGeneratorFactories
     * @param ContainerInterface $generalTypeGeneratorFactories
     * @param ContainerInterface $assetTypeGeneratorFactories
     * @param ContainerInterface $csFeatureTypeGeneratorFactories
     */
    public function __construct(
        AssetFieldHelper $assetFieldHelper,
        DocumentFieldHelper $documentFieldHelper,
        DataObjectFieldHelper $objectFieldHelper,
        LocaleServiceInterface $localeService,
        Factory $modelFactory,
        Translator $translator,
        ContainerInterface $dataObjectQueryTypeGeneratorFactories,
        ContainerInterface $dataObjectQueryOperatorFactories,
        ContainerInterface $dataObjectMutationTypeGeneratorFactories,
        ContainerInterface $dataObjectMutationOperatorFactories,
        ContainerInterface $documentElementQueryTypeGeneratorFactories,
        ContainerInterface $documentElementMutationTypeGeneratorFactories,
        ContainerInterface $generalTypeGeneratorFactories,
        ContainerInterface $assetTypeGeneratorFactories,
        ContainerInterface $csFeatureTypeGeneratorFactories
    )
    {
        $this->assetFieldHelper = $assetFieldHelper;
        $this->documentFieldHelper = $documentFieldHelper;
        $this->objectFieldHelper = $objectFieldHelper;
        $this->localeService = $localeService;
        $this->modelFactory = $modelFactory;
        $this->translator = $translator;
        $this->dataObjectQueryTypeGeneratorFactories = $dataObjectQueryTypeGeneratorFactories;
        $this->dataObjectQueryOperatorFactories = $dataObjectQueryOperatorFactories;
        $this->dataObjectMutationTypeGeneratorFactories = $dataObjectMutationTypeGeneratorFactories;
        $this->dataObjectMutationOperatorFactories = $dataObjectMutationOperatorFactories;
        $this->documentElementQueryTypeGeneratorFactories = $documentElementQueryTypeGeneratorFactories; //TODO rename this to query
        $this->documentElementMutationGeneratorFactories = $documentElementMutationTypeGeneratorFactories;
        $this->generalTypeGeneratorFactories = $generalTypeGeneratorFactories;
        $this->assetTypeGeneratorFactories = $assetTypeGeneratorFactories;
        $this->csFeatureTypeGeneratorFactories = $csFeatureTypeGeneratorFactories;
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
    public function buildDataObjectQueryDataConfig($attribute, $typeName, $fieldDefinition = null, $class = null, $container = null)
    {
        /** @var DataObjectQueryFieldConfigGeneratorInterface $factory */
        $factory = $this->dataObjectQueryTypeGeneratorFactories->get('typegenerator_dataobjectquerydatatype_' . $typeName);
        $result = $factory->getGraphQlFieldConfig($attribute, $fieldDefinition, $class, $container);
        return $result;
    }


    /**
     * @param $nodeDef
     * @param $typeName
     * @param ClassDefinition|\Pimcore\Model\DataObject\Fieldcollection\Definition $class
     * @param null $container
     * @return mixed
     */
    public function buildDataObjectMutationDataConfig($nodeDef, $class = null, $container = null)
    {
        /** @var DataObjectMutationFieldConfigGeneratorInterface $factory */
        $typeName = $nodeDef["attributes"]["dataType"];
        $factory = $this->dataObjectMutationTypeGeneratorFactories->get('typegenerator_dataobjectmutationdatatype_' . $typeName);
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
    public function buildDataObjectDataQueryResolver($attribute, Data $fieldDefinition = null, ClassDefinition $class = null)
    {
        $name = $fieldDefinition->getFieldtype();
        /** @var DataObjectQueryFieldConfigGeneratorInterface $factory */
        $factory = $this->dataObjectQueryTypeGeneratorFactories->get('typegenerator_dataobjectquerydatatype_' . $name);
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
    public function buildDataObjectDataQueryType(Data $fieldDefinition = null, ClassDefinition $class = null, $container = null)
    {
        $name = $fieldDefinition->getFieldtype();
        /** @var DataObjectQueryFieldConfigGeneratorInterface $factory */
        $factory = $this->dataObjectQueryTypeGeneratorFactories->get('typegenerator_dataobjectquerydatatype_' . $name);
        $result = $factory->getFieldType($fieldDefinition, $class, $container);

        return $result;
    }

    /**
     * @param $elementName
     * @return mixed
     */
    public function buildDocumentElementDataQueryType($elementName)
    {
        $factory = $this->documentElementQueryTypeGeneratorFactories->get('typegenerator_documentelementquerydatatype_' . $elementName);
        $result = $factory->getFieldType();

        return $result;
    }

    /**
     * @param $elementName
     * @return mixed
     */
    public function buildDocumentElementDataMutationType($elementName)
    {
        $factory = $this->documentElementMutationGeneratorFactories->get('typegenerator_documentelementmutationdatatype_' . $elementName);
        $result = $factory->getDocumentElementMutationFieldConfig();

        return $result;
    }

    /**
     * @param $elementName
     * @return mixed
     */
    public function buildCsFeatureDataQueryType($elementName)
    {
        $factory = $this->csFeatureTypeGeneratorFactories->get('typegenerator_csfeaturequerydatatype_' . $elementName);
        $result = $factory->getFieldType();

        return $result;
    }


    /**
     * @param $typeName
     *
     * @return bool
     */
    public function supportsDataObjectQueryDataType($typeName)
    {
        return $this->dataObjectQueryTypeGeneratorFactories->has('typegenerator_dataobjectquerydatatype_' . $typeName);
    }

    /**
     * @param $typeName
     *
     * @return bool
     */
    public function supportsDataObjectMutationDataType($typeName)
    {
        return $this->dataObjectMutationTypeGeneratorFactories->has('typegenerator_dataobjectmutationdatatype_' . $typeName);
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
    public function buildDataObjectQueryOperatorConfig($typeName, $nodeDef, ClassDefinition $class = null, $container = null, $params = [])
    {
        $typeName = strtolower($typeName);
        /** @var DataObjectQueryFieldConfigGeneratorInterface $factory */
        $factory = $this->dataObjectQueryTypeGeneratorFactories->get('typegenerator_queryoperator_' . $typeName);
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
    public function buildDataObjectMutationOperatorConfig($typeName, $nodeDef, ClassDefinition $class = null, $container = null, $params = [])
    {
        $typeName = strtolower($typeName);

        // $factory = $this->mutationTypeGeneratorFactories->get('typegenerator_mutationoperator_' . $typeName);
        $factory = $this->dataObjectMutationOperatorFactories->get($typeName);
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
    public function buildDataObjectOperatorQueryType($mode, $typeName, $nodeDef, ClassDefinition $class = null, $container = null, $params = [])
    {
        $typeName = strtolower($typeName);
        /** @var DataObjectQueryFieldConfigGeneratorInterface $factory */
        $factory = $this->dataObjectQueryTypeGeneratorFactories->get('typegenerator_operator_' . $typeName);
        $result = $factory->getGraphQlOperatorConfig($mode, $typeName, $nodeDef, $class, $container, $params);

        return $result;
    }


    /**
     * @param $typeName
     * @return mixed
     * @throws \Exception
     */
    public function buildGeneralType($typeName)
    {
        $factory = $this->generalTypeGeneratorFactories->get($typeName);
        $result = $factory->build();
        return $result;
    }

    /**
     * @param $typeName
     * @return mixed
     * @throws \Exception
     */
    public function buildAssetType($typeName)
    {
        $factory = $this->assetTypeGeneratorFactories->get($typeName);
        $result = $factory->build();
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
        $factory = $this->dataObjectQueryOperatorFactories->get($typeName);

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
    public function getSupportedDataObjectQueryDataTypes()
    {
        return $this->supportedDataObjectQueryDataTypes;
    }

    /**
     * @param $supportedDocumentElementQueryDataTypes
     */
    public function setSupportedDocumentElementQueryDataTypes($supportedDocumentElementQueryDataTypes)
    {
        $this->supportedDocumentElementQueryDataTypes = $supportedDocumentElementQueryDataTypes;
    }

    /**
     * @param $supportedDocumentElementMutationDataTypes
     */
    public function setSupportedDocumentElementMutationDataTypes($supportedDocumentElementMutationDataTypes)
    {
        $this->supportedDocumentElementMutationDataTypes = $supportedDocumentElementMutationDataTypes;
    }


    /**
     * @param $supportedCsFeatureQueryDataTypes
     */
    public function setSupportedCsFeatureQueryDataTypes($supportedCsFeatureQueryDataTypes)
    {
        $this->supportedCsFeatureQueryDataTypes = $supportedCsFeatureQueryDataTypes;
    }


    /**
     * @param array $generalTypes
     */
    public function setSupportedGeneralTypes($generalTypes)
    {
        $this->generalTypes = $generalTypes;
    }


    /**
     * @return array
     */
    public function getSupportedDocumentElementQueryDataTypes()
    {
        return $this->supportedDocumentElementQueryDataTypes;
    }

    /**
     * @return array
     */
    public function getSupportedDocumentElementMutationDataTypes()
    {
        return $this->supportedDocumentElementMutationDataTypes;
    }

    /**
     * @return array
     */
    public function getSupportedCsFeatureQueryDataTypes()
    {
        return $this->supportedCsFeatureQueryDataTypes;
    }

    /**
     * @param $supportedDataObjectQueryDataTypes
     */
    public function setSupportedDataObjectQueryDataTypes($supportedDataObjectQueryDataTypes)
    {
        $this->supportedDataObjectQueryDataTypes = $supportedDataObjectQueryDataTypes;
    }

    /**
     * @return array
     */
    public function getSupportedDataObjectMutationDataTypes(): array
    {
        return $this->supportedDataObjectMutationDataTypes;
    }

    /**
     * @param array $supportedDataObjectMutationDataTypes
     */
    public function setSupportedDataObjectMutationDataTypes(array $supportedDataObjectMutationDataTypes): void
    {
        $this->supportedDataObjectMutationDataTypes = $supportedDataObjectMutationDataTypes;
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
     * @return DocumentFieldHelper
     */
    public function getDocumentFieldHelper()
    {
        return $this->documentFieldHelper;
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
        return $this->dataObjectQueryTypeGeneratorFactories;
    }

    /**
     * @return ContainerInterface
     */
    public function getQueryOperatorFactories(): ContainerInterface
    {
        return $this->dataObjectQueryOperatorFactories;
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
    public function registerDataObjectDataTypes($dataTypes)
    {
        $this->dataObjectDataTypes = $dataTypes;
    }

    /**
     * @param $dataTypes
     */
    public function registerAssetDataTypes($dataTypes)
    {
        $this->assetDataTypes = $dataTypes;
    }

    /**
     * @param $dataTypes
     */
    public function registerDocumentDataTypes($dataTypes)
    {
        $this->documentDataTypes = $dataTypes;
    }

    /**
     * @param $dataTypes
     */
    public function registerPropertyDataTypes($dataTypes)
    {
        $this->propertyDataTypes = $dataTypes;
    }


    /**
     * @param $dataTypes
     */
    public function registerClassificationStoreDataTypes($dataTypes)
    {
        $this->classificationStoreDataTypes = $dataTypes;
    }

    /**
     * @param $typename
     * @return mixed
     * @throws \Exception
     */
    public function getAssetTypeDefinition($typename)
    {
        if (isset($this->assetDataTypes[$typename])) {
            return $this->assetDataTypes[$typename];
        }
        throw new ClientSafeException("unknown asset type: " . $typename);
    }


    /**
     * @param $typename
     * @return mixed
     * @throws \Exception
     */
    public function getClassificationStoreTypeDefinition($typename)
    {
        if (isset($this->classificationStoreDataTypes[$typename])) {
            return $this->classificationStoreDataTypes[$typename];
        }
        throw new ClientSafeException("unknown classificationstore type: " . $typename);
    }


    /**
     * @param $typename
     * @return mixed
     * @throws \Exception
     */
    public function getDataObjectTypeDefinition($typename)
    {
        if (isset($this->dataObjectDataTypes[$typename])) {
            return $this->dataObjectDataTypes[$typename];
        }
        throw new ClientSafeException("unknown dataobject type: " . $typename);
    }


    /**
     * @param $typename
     * @return mixed
     * @throws \Exception
     */
    public function getDocumentTypeDefinition($typename)
    {
        if (isset($this->documentDataTypes[$typename])) {
            return $this->documentDataTypes[$typename];
        }
        throw new ClientSafeException("unknown document type: " . $typename);
    }

    /**
     * @param $typename
     * @return mixed
     * @throws \Exception
     */
    public function getPropertyTypeDefinition($typename)
    {
        if (isset($this->propertyDataTypes[$typename])) {
            return $this->propertyDataTypes[$typename];
        }
        throw new ClientSafeException("unknown property type: " . $typename);
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
     * @param string $attribute
     * @param array $args
     * @return \stdclass|null
     * @throws \Exception
     */
    public static function setValue($object, $attribute, $callback)
    {

        $result = null;
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
            $brickDescriptor = null;

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
     * @param BaseDescriptor $descriptor
     * @param Data $fieldDefinition
     * @param string $attribute
     * @param array $args
     * @return \stdclass|null
     */
    public static function resolveValue(BaseDescriptor $descriptor, Data $fieldDefinition, $attribute, $args = [])
    {
        $getter = 'get' . ucfirst($fieldDefinition->getName());
        $objectId = $descriptor["id"];
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

        if ($descriptor instanceof FieldcollectionDescriptor) {

            $descriptorData = $descriptor->getArrayCopy();
            $fcFieldNameGetter = "get" . ucfirst($descriptorData['__fcFieldname']);
            $fcData = $object->$fcFieldNameGetter();
            if ($fcData) {
                $items = $fcData->getItems();
                $idx = $descriptorData["__itemIdx"];
                $itemData = $items[$idx];
                if (is_array($args) && isset($args["language"])) {
                    $result = $itemData->$getter($args["language"]);
                } else {
                    $result = $itemData->$getter();
                }
            }
        }

        if ($descriptor instanceof BlockDescriptor) {
            $descriptorData = $descriptor->getArrayCopy();
            $blockData = null;

            if (isset($descriptorData['__fcFieldname']) && $descriptorData['__fcFieldname']) {
                $fcFieldNameGetter = "get" . ucfirst($descriptorData['__fcFieldname']);
                $fcData = $object->$fcFieldNameGetter();

                if ($fcData) {
                    $items = $fcData->getItems();
                    $idx = $descriptorData["__itemIdx"];
                    $itemData = $items[$idx];
                    $result = [];

                    $blockGetter = "get" . ucfirst($descriptorData['__blockName']);
                    $blockData = call_user_func_array([$itemData, $blockGetter], $descriptorData['args'] ?? []);;
                }
            }
            elseif (isset($descriptorData['__brickType']) && $descriptorData['__brickType']) {
                $context = ["object" => $object];
                $brickDescriptor = null;

                $brickType = $descriptorData['__brickType'];
                $brickKey = $descriptorData['__brickKey'];

                $key = \Pimcore\Model\DataObject\Service::getFieldForBrickType($object->getclass(), $brickType);

                $brickClass = Definition::getByKey($brickType);

                if (!$brickClass) {
                    return null;
                }

                $context['outerFieldname'] = $key;

                $def = $brickClass->getFieldDefinition($brickKey, $context);

                if (!$def) {
                    return null;
                }

                if (!empty($key)) {
                    $blockData = \Pimcore\Bundle\DataHubBundle\GraphQL\Service::getValueForObject($object, $key, $brickType, $brickKey, $def, $context, $brickDescriptor, $args);
                }
            }
            else {
                $blockGetter = "get".ucfirst($descriptorData['__blockName']);
                $isLocalizedField = self::isLocalizedField($container, $fieldDefinition->getName());
                $blockData = $object->$blockGetter($isLocalizedField && isset($descriptorData['args']['language']) ?  $descriptorData['args']['language'] : null);                
            }

            if ($blockData) {
                $index = $descriptorData["__blockIndex"];
                $itemData = $blockData[$index];
                $result = $itemData[$descriptorData['__blockFieldName']]->getData();

                if (isset($descriptorData['__localized']) && $descriptorData['__localized']) {
                    $result = $result->getLocalizedValue($descriptorData['__localized'], $args['language'] ?? null);
                }
            }
        } else if (substr($attribute, 0, 1) == '~') {
            // key value, ignore for now
        } elseif (count($attributeParts) > 1) {
            // TODO once the datahub gets integrated into the core we should try to share this code
            // with Pimcore\Model\DataObject\Service::gridObjectData
            $context = ["object" => $object];
            $brickDescriptor = null;

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
            $isLocalizedField = self::isLocalizedField($container, $fieldDefinition->getName());
            $result = $container->$getter($isLocalizedField && isset($args['language']) ?  $args['language'] : null);

        }
        return $result;
    }
    
    /**
     * Check whether given field in container is localized
     * @param Concrete|AbstractData|\Pimcore\Model\DataObject\Objectbrick\Data\AbstractData $container
     * @param string $fieldName
     * @return bool
     */
    private static function isLocalizedField($container,$fieldName): bool {
        $containerDefinition = null;

        if ($container instanceof Concrete) {
            $containerDefinition = $container->getClass();
        } else if ($container instanceof AbstractData || $container instanceof \Pimcore\Model\DataObject\Objectbrick\Data\AbstractData) {
            $containerDefinition = $container->getDefinition();
        }

        if ($containerDefinition) {
            if ($lfDefs = $containerDefinition->getFieldDefinition('localizedfields')) {
                if ($lfDefs->getFieldDefinition($fieldName)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return ContainerInterface
     */
    public function getDataObjectMutationTypeGeneratorFactories(): ContainerInterface
    {
        return $this->dataObjectMutationTypeGeneratorFactories;
    }

    /**
     * @param ContainerInterface $mutationTypeGeneratorFactories
     */
    public function setDataObjectMutationTypeGeneratorFactories(ContainerInterface $dataObjectMutationTypeGeneratorFactories): void
    {
        $this->dataObjectMutationTypeGeneratorFactories = $dataObjectMutationTypeGeneratorFactories;
    }

    /**
     * @return ContainerInterface
     */
    public function getDataObjectMutationOperatorFactories(): ContainerInterface
    {
        return $this->dataObjectMutationOperatorFactories;
    }

    /**
     * @param ContainerInterface $dataObjectMutationOperatorFactories
     */
    public function setDataObjectMutationOperatorFactories(ContainerInterface $dataObjectMutationOperatorFactories): void
    {
        $this->dataObjectMutationOperatorFactories = $dataObjectMutationOperatorFactories;
    }

    /**
     * @return array
     */
    public function getDataObjectDataTypes(): array
    {
        return $this->dataObjectDataTypes;
    }

    /**
     * @param array $data
     * @param AbstractElement $target
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     */
    public function extractData($data, $target, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
        $fieldHelper = null;
        if ($target instanceof Document) {
            $fieldHelper = $this->getDocumentFieldHelper();
        } else if ($target instanceof Asset) {
            $fieldHelper = $this->getAssetFieldHelper();
        } else if ($target instanceof AbstractObject) {
            $fieldHelper = $this->getObjectFieldHelper();
        }

        if ($fieldHelper) {
            $fieldHelper->extractData($data, $target, $args, $context, $resolveInfo);
        }
    }


    /**
     * @param string $type
     * @return bool
     */
    public function querySchemaEnabled(string $type) {
        $context = Runtime::get('datahub_context');
        /** @var  $configuration Configuration */
        $configuration = $context["configuration"];
        if ($type === "object") {
            $types = $configuration->getConfiguration()["schema"]["queryEntities"];
            $enabled = count($types) > 0;
        } else {
            $enabled = $configuration->getSpecialEntities()[$type]["read"] ?? false;
        }
        return $enabled;
    }

    public function setTagManagerListener($tagManagerListener) {
        $this->tagManagerListener = $tagManagerListener;
    }

    /**
     * @return mixed
     */
    public function getTagManagerListener()
    {
        return $this->tagManagerListener;
    }


}
