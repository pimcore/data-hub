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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PCL
 */
declare(strict_types=1);

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
use Pimcore\Cache\RuntimeCache;
use Pimcore\DataObject\GridColumnConfig\ConfigElementInterface;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;
use Pimcore\Model\DataObject\Objectbrick\Definition;
use Pimcore\Model\Document;
use Pimcore\Model\Element\ElementInterface;
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
    protected $documentElementMutationGeneratorFactories;

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

    protected ContainerInterface $translationTypeGeneratorFactories;

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

    protected array $translationDataTypes = [];

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
        ContainerInterface $csFeatureTypeGeneratorFactories,
        ContainerInterface $translationTypeGeneratorFactories
    ) {
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
        $this->translationTypeGeneratorFactories = $translationTypeGeneratorFactories;
    }

    /**
     * @param string $attribute
     * @param string $typeName
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|\Pimcore\Model\DataObject\Fieldcollection\Definition|null $class
     * @param object|null $container
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
     * @param array $nodeDef
     * @param ClassDefinition|\Pimcore\Model\DataObject\Fieldcollection\Definition $class
     * @param object|null $container
     *
     * @return array
     */
    public function buildDataObjectMutationDataConfig($nodeDef, $class = null, $container = null)
    {
        $typeName = $nodeDef['attributes']['dataType'];
        /** @var DataObjectMutationFieldConfigGeneratorInterface $factory */
        $factory = $this->dataObjectMutationTypeGeneratorFactories->get('typegenerator_dataobjectmutationdatatype_' . $typeName);
        $result = $factory->getGraphQlMutationFieldConfig($nodeDef, $class, $container);

        return $result;
    }

    /**
     * @param string $attribute
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|null $class
     *
     * @return callable(mixed $value, array $args, array $context, \GraphQL\Type\Definition\ResolveInfo $info): mixed
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
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|null $class
     * @param object|null $container
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
     * @param string $elementName
     *
     * @return mixed
     */
    public function buildDocumentElementDataQueryType($elementName)
    {
        $factory = $this->documentElementQueryTypeGeneratorFactories->get('typegenerator_documentelementquerydatatype_' . $elementName);
        $result = $factory->getFieldType();

        return $result;
    }

    /**
     * @param string $elementName
     *
     * @return mixed
     */
    public function buildDocumentElementDataMutationType($elementName)
    {
        $factory = $this->documentElementMutationGeneratorFactories->get('typegenerator_documentelementmutationdatatype_' . $elementName);
        $result = $factory->getDocumentElementMutationFieldConfig();

        return $result;
    }

    /**
     * @param string $elementName
     *
     * @return mixed
     */
    public function buildCsFeatureDataQueryType($elementName)
    {
        $factory = $this->csFeatureTypeGeneratorFactories->get('typegenerator_csfeaturequerydatatype_' . $elementName);
        $result = $factory->getFieldType();

        return $result;
    }

    /**
     * @param string $typeName
     *
     * @return bool
     */
    public function supportsDataObjectQueryDataType($typeName)
    {
        return $this->dataObjectQueryTypeGeneratorFactories->has('typegenerator_dataobjectquerydatatype_' . $typeName);
    }

    /**
     * @param string $typeName
     *
     * @return bool
     */
    public function supportsDataObjectMutationDataType($typeName)
    {
        return $this->dataObjectMutationTypeGeneratorFactories->has('typegenerator_dataobjectmutationdatatype_' . $typeName);
    }

    /**
     * @param string $typeName
     * @param array $nodeDef
     * @param ClassDefinition|null $class
     * @param object|null $container
     * @param array $params
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
     * @param string $typeName
     * @param array $nodeDef
     * @param ClassDefinition|null $class
     * @param object|null $container
     * @param array $params
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function buildDataObjectMutationOperatorConfig($typeName, $nodeDef, ClassDefinition $class = null, $container = null, $params = [])
    {
        $typeName = strtolower($typeName);

        // $factory = $this->mutationTypeGeneratorFactories->get('typegenerator_mutationoperator_' . $typeName);
        $factory = $this->dataObjectMutationOperatorFactories->get($typeName);
        $context = RuntimeCache::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        $configGenerator = $factory->build($nodeDef['attributes'], $context);
        $result = $configGenerator->getGraphQlMutationOperatorConfig($nodeDef, $class, $container, $params);

        return $result;
    }

    /**
     * @param string $mode
     * @param string $typeName
     * @param array $nodeDef
     * @param ClassDefinition|null $class
     * @param object|null $container
     * @param array $params
     *
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
     * @param string $typeName
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function buildGeneralType($typeName)
    {
        $factory = $this->generalTypeGeneratorFactories->get($typeName);
        $result = $factory->build();

        return $result;
    }

    /**
     * @param string $typeName
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function buildAssetType($typeName)
    {
        $factory = $this->assetTypeGeneratorFactories->get($typeName);
        $result = $factory->build();

        return $result;
    }

    /**
     * @throws \Exception
     */
    public function buildTranslationType(string $typeName): mixed
    {
        $factory = $this->translationTypeGeneratorFactories->get($typeName);
        $result = $factory->build();

        return $result;
    }

    /**
     * @param string $typeName
     * @param array|null $attributes
     * @param ClassDefinition|null $class
     * @param object|null $container
     *
     * @return Query\Operator\OperatorInterface
     */
    public function buildQueryOperator($typeName, $attributes = null, ClassDefinition $class = null, $container = null)
    {
        $typeName = strtolower($typeName);
        /** @var OperatorFactoryInterface $factory */
        $factory = $this->dataObjectQueryOperatorFactories->get($typeName);

        $context = RuntimeCache::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        $result = $factory->build($attributes, $context);

        return $result;
    }

    /**
     * @param ConfigElementInterface $nodeConfig
     *
     * @return DefaultValue|Query\Operator\OperatorInterface
     *
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
            $context = RuntimeCache::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
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
     * @param array $supportedDocumentElementQueryDataTypes
     */
    public function setSupportedDocumentElementQueryDataTypes($supportedDocumentElementQueryDataTypes)
    {
        $this->supportedDocumentElementQueryDataTypes = $supportedDocumentElementQueryDataTypes;
    }

    /**
     * @param array $supportedDocumentElementMutationDataTypes
     */
    public function setSupportedDocumentElementMutationDataTypes($supportedDocumentElementMutationDataTypes)
    {
        $this->supportedDocumentElementMutationDataTypes = $supportedDocumentElementMutationDataTypes;
    }

    /**
     * @param array $supportedCsFeatureQueryDataTypes
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
     * @param array $supportedDataObjectQueryDataTypes
     */
    public function setSupportedDataObjectQueryDataTypes($supportedDataObjectQueryDataTypes)
    {
        $this->supportedDataObjectQueryDataTypes = $supportedDataObjectQueryDataTypes;
    }

    public function getSupportedDataObjectMutationDataTypes(): array
    {
        return $this->supportedDataObjectMutationDataTypes;
    }

    public function setSupportedDataObjectMutationDataTypes(array $supportedDataObjectMutationDataTypes): void
    {
        $this->supportedDataObjectMutationDataTypes = $supportedDataObjectMutationDataTypes;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function getElementFromArrayObject($value)
    {
        if ($value instanceof \ArrayObject) {
            $value = $value->getArrayCopy();
            if (isset($value['__elementType'])) {
                $value = \Pimcore\Model\Element\Service::getElementById($value['__elementType'], $value['id']);
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

    public function getQueryTypeGeneratorFactories(): ContainerInterface
    {
        return $this->dataObjectQueryTypeGeneratorFactories;
    }

    public function getQueryOperatorFactories(): ContainerInterface
    {
        return $this->dataObjectQueryOperatorFactories;
    }

    public function getLocaleService(): LocaleServiceInterface
    {
        return $this->localeService;
    }

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
     * @param array $dataTypes
     */
    public function registerDataObjectDataTypes($dataTypes)
    {
        $this->dataObjectDataTypes = $dataTypes;
    }

    /**
     * @param array $dataTypes
     */
    public function registerAssetDataTypes($dataTypes)
    {
        $this->assetDataTypes = $dataTypes;
    }

    public function registerTranslationDataTypes(array $dataTypes)
    {
        $this->translationDataTypes = $dataTypes;
    }

    /**
     * @param array $dataTypes
     */
    public function registerDocumentDataTypes($dataTypes)
    {
        $this->documentDataTypes = $dataTypes;
    }

    /**
     * @param array $dataTypes
     */
    public function registerPropertyDataTypes($dataTypes)
    {
        $this->propertyDataTypes = $dataTypes;
    }

    /**
     * @param array $dataTypes
     */
    public function registerClassificationStoreDataTypes($dataTypes)
    {
        $this->classificationStoreDataTypes = $dataTypes;
    }

    /**
     * @param string $typeName
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getAssetTypeDefinition($typeName)
    {
        if (isset($this->assetDataTypes[$typeName])) {
            return $this->assetDataTypes[$typeName];
        }
        throw new ClientSafeException('unknown asset type: ' . $typeName);
    }

    /**
     * @param string $typeName
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getClassificationStoreTypeDefinition($typeName)
    {
        if (isset($this->classificationStoreDataTypes[$typeName])) {
            return $this->classificationStoreDataTypes[$typeName];
        }
        throw new ClientSafeException('unknown classificationstore type: ' . $typeName);
    }

    /**
     * @param string $typeName
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getDataObjectTypeDefinition($typeName)
    {
        if (isset($this->dataObjectDataTypes[$typeName])) {
            return $this->dataObjectDataTypes[$typeName];
        }
        throw new ClientSafeException('unknown dataobject type: ' . $typeName);
    }

    /**
     * @param string $typeName
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getDocumentTypeDefinition($typeName)
    {
        if (isset($this->documentDataTypes[$typeName])) {
            return $this->documentDataTypes[$typeName];
        }
        throw new ClientSafeException('unknown document type: ' . $typeName);
    }

    /**
     * @param string $typeName
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getPropertyTypeDefinition($typeName)
    {
        if (isset($this->propertyDataTypes[$typeName])) {
            return $this->propertyDataTypes[$typeName];
        }
        throw new ClientSafeException('unknown property type: ' . $typeName);
    }

    /**
     * gets value for given object and getter, including inherited values
     *
     * @static
     *
     * @param Concrete $object
     * @param string $key
     * @param string|null $brickType
     * @param string|null $brickKey
     * @param Data|null $fieldDefinition
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
                    $innerContainer = $brickDescriptor['innerContainer'] ?? 'localizedfields';
                    $localizedFields = $value->{'get' . ucfirst($innerContainer)}();
                    $brickDefinition = Definition::getByKey($brickType);
                    /** @var Data\Localizedfields $fieldDefinitionLocalizedFields */
                    $fieldDefinitionLocalizedFields = $brickDefinition->getFieldDefinition('localizedfields');
                    $fieldDefinition = $fieldDefinitionLocalizedFields->getFieldDefinition($brickKey);
                    $value = $localizedFields->getLocalizedValue($brickDescriptor['brickfield'], isset($args['language']) ? $args['language'] : null);
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
                if (!($parent instanceof Concrete)) {
                    $parent = Concrete::getById($parent->getId());
                }

                return self::getValueForObject($parent, $key, $brickType, $brickKey, $fieldDefinition, $context, $brickDescriptor);
            }
        }

        return $value;
    }

    /**
     * @param Concrete|null $object
     * @param string $attribute
     * @param \Closure $callback
     *
     * @return \stdclass|null
     *
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

        $attributeParts = explode('~', $attribute);

        if (substr($attribute, 0, 1) === '~') {
            // key value, ignore for now
        } elseif (count($attributeParts) > 1) {
            // TODO once the datahub gets integrated into the core we should try to share this code
            // with Pimcore\Model\DataObject\Service::gridObjectData
            $context = ['object' => $object];
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
                    $innerContainer = $brickDescriptor['innerContainer'] ?? 'localizedfields';
                    /** @var Data\Localizedfields $localizedFields */
                    $localizedFields = $brickClass->getFieldDefinition($innerContainer);
                    $def = $localizedFields->getFieldDefinition($brickDescriptor['brickfield']);
                }
            } else {
                $def = $brickClass->getFieldDefinition($brickKey, $context);
            }

            if (!empty($key)) {
                // if the definition is not set try to get the definition from localized fields
                if (!$def) {
                    /** @var Data\Localizedfields|null $locFields */
                    $locFields = $object->getClass()->getFieldDefinition('localizedfields');
                    if ($locFields) {
                        $def = $locFields->getFieldDefinition($key, $context);
                    }
                }
                $brickGetter = 'get' . ucfirst($key);

                $brickContainer = $object->$brickGetter();
                $subBrickGetter = 'get' . ucfirst($brickType);
                $subBrickSetter = 'set' . ucfirst($brickType);
                $subBrickType = $brickContainer->$subBrickGetter();

                if (!$subBrickType) {
                    /** @var AbstractData $brickClass */
                    $brickClass = 'Pimcore\\Model\\DataObject\\Objectbrick\\Data\\' . ucfirst($brickType);
                    $subBrickType = new $brickClass($object);
                    $subBrickSetter = 'set' . ucfirst($brickType);
                    $brickContainer->$subBrickSetter($subBrickType);
                }

                $innerSetter = 'set' . ucfirst($def->getName());
                $result = $callback($subBrickType, $innerSetter, $def->getName());

                $brickContainer->$subBrickSetter($subBrickType);

                return $result;
            }
        } elseif (method_exists($container, $setter)) {
            $result = $callback($container, $setter, $attribute);
        }

        return $result;
    }

    /**
     * @param BaseDescriptor $descriptor
     * @param Data $fieldDefinition
     * @param string $attribute
     * @param array $args
     *
     * @return mixed
     */
    public static function resolveValue(BaseDescriptor $descriptor, Data $fieldDefinition, $attribute, $args = [])
    {
        $getter = 'get' . ucfirst($fieldDefinition->getName());
        $objectId = $descriptor['id'];
        $object = Concrete::getById($objectId);
        if (!$object) {
            return null;
        }
        $result = null;
        $container = $object;

        $attributeParts = explode('~', $attribute);

        if ($descriptor instanceof FieldcollectionDescriptor) {
            $descriptorData = $descriptor->getArrayCopy();
            $fcFieldNameGetter = 'get' . ucfirst($descriptorData['__fcFieldname']);
            $fcData = $object->$fcFieldNameGetter();
            if ($fcData) {
                $items = $fcData->getItems();
                $idx = $descriptorData['__itemIdx'];
                $itemData = $items[$idx];
                if (is_array($args) && isset($args['language'])) {
                    $result = $itemData->$getter($args['language']);
                } else {
                    $result = $itemData->$getter();
                }
            }
        } elseif ($descriptor instanceof BlockDescriptor) {
            $descriptorData = $descriptor->getArrayCopy();
            $blockData = null;

            if (isset($descriptorData['__fcFieldname']) && $descriptorData['__fcFieldname']) {
                $fcFieldNameGetter = 'get' . ucfirst($descriptorData['__fcFieldname']);
                $fcData = $object->$fcFieldNameGetter();

                if ($fcData) {
                    $items = $fcData->getItems();
                    $idx = $descriptorData['__itemIdx'];
                    $itemData = $items[$idx];
                    $result = [];

                    $blockGetter = 'get' . ucfirst($descriptorData['__blockName']);
                    $blockData = call_user_func_array([$itemData, $blockGetter], $descriptorData['args'] ?? []);
                }
            } elseif (isset($descriptorData['__brickType']) && $descriptorData['__brickType']) {
                $context = ['object' => $object];
                $brickDescriptor = $descriptorData['__brickDescriptor'] ?? null;

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
                    $blockData = self::getValueForObject($object, $key, $brickType, $brickKey, $def, $context, $brickDescriptor, $descriptorData['args'] ?? []);
                }
            } else {
                $blockGetter = 'get'.ucfirst($descriptorData['__blockName']);
                $isLocalizedField = self::isLocalizedField($container, $fieldDefinition->getName());
                if ($isLocalizedField) {
                    $blockData = $object->$blockGetter($descriptorData['args']['language'] ?? null);
                } else {
                    $blockData = $object->$blockGetter();
                }
            }

            if ($blockData) {
                $index = $descriptorData['__blockIndex'];
                $itemData = $blockData[$index];
                $result = $itemData[$descriptorData['__blockFieldName']]->getData();

                if (isset($descriptorData['__localized']) && $descriptorData['__localized']) {
                    $result = $result->getLocalizedValue($descriptorData['__localized'], $args['language'] ?? null);
                }
            }
        } elseif (substr($attribute, 0, 1) == '~') {
            // key value, ignore for now
        } elseif (count($attributeParts) > 1) {
            // TODO once the datahub gets integrated into the core we should try to share this code
            // with Pimcore\Model\DataObject\Service::gridObjectData
            $context = ['object' => $object];
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
                $innerContainer = $brickDescriptor['innerContainer'] ?? 'localizedfields';
                /** @var Data\Localizedfields $localizedFields */
                $localizedFields = $brickClass->getFieldDefinition($innerContainer);
                $def = $localizedFields->getFieldDefinition($brickDescriptor['brickfield']);
            } else {
                $def = $brickClass->getFieldDefinition($brickKey, $context);
            }

            if (!empty($key)) {
                // if the definition is not set try to get the definition from localized fields
                if (!$def) {
                    /** @var Data\Localizedfields|null $locFields */
                    $locFields = $object->getClass()->getFieldDefinition('localizedfields');
                    if ($locFields) {
                        $def = $locFields->getFieldDefinition($key, $context);
                    }
                }
                $value = self::getValueForObject($object, $key, $brickType, $brickKey, $def, $context, $brickDescriptor, $args);

                return $value;
            }
        } elseif (method_exists($container, $getter)) {
            $isLocalizedField = self::isLocalizedField($container, $fieldDefinition->getName());
            if ($isLocalizedField) {
                $result = $container->$getter($args['language'] ?? null);
            } else {
                $result = $container->$getter();
            }
        }

        return $result;
    }

    /**
     * Check whether given field in container is localized
     *
     * @param object $container
     * @param string $fieldName
     *
     * @return bool
     */
    private static function isLocalizedField($container, $fieldName): bool
    {
        $containerDefinition = null;

        if ($container instanceof Concrete) {
            $containerDefinition = $container->getClass();
        } elseif ($container instanceof AbstractData) {
            $containerDefinition = $container->getDefinition();
        }

        if ($containerDefinition) {
            /** @var Data\Localizedfields|null $lfDefs */
            $lfDefs = $containerDefinition->getFieldDefinition('localizedfields');
            if ($lfDefs?->getFieldDefinition($fieldName)) {
                return true;
            }
        }

        return false;
    }

    public function getDataObjectMutationTypeGeneratorFactories(): ContainerInterface
    {
        return $this->dataObjectMutationTypeGeneratorFactories;
    }

    public function setDataObjectMutationTypeGeneratorFactories(ContainerInterface $dataObjectMutationTypeGeneratorFactories): void
    {
        $this->dataObjectMutationTypeGeneratorFactories = $dataObjectMutationTypeGeneratorFactories;
    }

    public function getDataObjectMutationOperatorFactories(): ContainerInterface
    {
        return $this->dataObjectMutationOperatorFactories;
    }

    public function setDataObjectMutationOperatorFactories(ContainerInterface $dataObjectMutationOperatorFactories): void
    {
        $this->dataObjectMutationOperatorFactories = $dataObjectMutationOperatorFactories;
    }

    public function getDataObjectDataTypes(): array
    {
        return $this->dataObjectDataTypes;
    }

    /**
     * @param mixed $data
     * @param ElementInterface $target
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     */
    public function extractData($data, $target, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $fieldHelper = null;
        if ($target instanceof Document) {
            $fieldHelper = $this->getDocumentFieldHelper();
        } elseif ($target instanceof Asset) {
            $fieldHelper = $this->getAssetFieldHelper();
        } elseif ($target instanceof AbstractObject) {
            $fieldHelper = $this->getObjectFieldHelper();
        }

        if ($fieldHelper) {
            $fieldHelper->extractData($data, $target, $args, $context, $resolveInfo);
        }
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function querySchemaEnabled(string $type)
    {
        $context = RuntimeCache::get('datahub_context');
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];
        if ($type === 'object') {
            $types = $configuration->getConfiguration()['schema']['queryEntities'];
            $enabled = count($types) > 0;
        } else {
            $enabled = $configuration->getSpecialEntities()[$type]['read'] ?? false;
        }

        return $enabled;
    }
}
