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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Mutation;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\MutationTypeEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\MutationEvents;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DataObjectFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\PermissionInfoTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Logger;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Folder;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document;
use Pimcore\Model\Factory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MutationType extends ObjectType
{

    use ServiceTrait;
    use PermissionInfoTrait;

    /**
     * @var LocaleServiceInterface
     */
    protected $localeService;
    /**
     * @var Factory
     */
    protected $modelFactory;
    protected $typeCache = [];
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * MutationType constructor.
     * @param Service $graphQlService
     * @param LocaleServiceInterface $localeService
     * @param Factory $modelFactory
     * @param EventDispatcherInterface $eventDispatcher
     * @param array $config
     * @param array $context
     * @throws \Exception
     */
    public function __construct(Service $graphQlService, LocaleServiceInterface $localeService, Factory $modelFactory, EventDispatcherInterface $eventDispatcher, $config = [], $context = [])
    {
        if (!isset($config['name'])) {
            $config['name'] = 'Mutations';
        }
        $this->setGraphQLService($graphQlService);
        $this->localeService = $localeService;
        $this->modelFactory = $modelFactory;
        $this->eventDispatcher = $eventDispatcher;

        $this->build($config, $context);
        parent::__construct($config);
    }

    /**
     * @param array $config
     * @param array $context
     *
     * @throws \Exception
     */
    public function build(&$config = [], $context = [])
    {
        $config["fields"] = [];
        $event = new MutationTypeEvent(
            $this,
            $config,
            $context
        );
        $this->eventDispatcher->dispatch(MutationEvents::PRE_BUILD, $event);

        $config = $event->getConfig();
        $context = $event->getContext();

        $this->buildDataObjectMutations($config, $context);
        $this->buildCreateAssetMutation($config, $context);
        $this->buildUpdateAssetMutation($config, $context);
        $this->buildCreateFolderMutation("asset", $config, $context);
        $this->buildCreateFolderMutation("object", $config, $context);
        $this->buildCreateFolderMutation("document", $config, $context);
        $this->buildUpdateFolderMutation("asset", $config, $context);
        $this->buildUpdateFolderMutation("object", $config, $context);
        $this->buildUpdateFolderMutation("document", $config, $context);
        $this->buildDeleteAssetMutation($config, $context);
        $this->buildDeleteDocumentMutation($config, $context);
        $this->buildDeleteFolderMutation("asset", $config, $context);
        $this->buildDeleteFolderMutation("document", $config, $context);
        $this->buildDeleteFolderMutation("object", $config, $context);

        $event->setConfig($config);
        $event->setContext($context);
        $this->eventDispatcher->dispatch(MutationEvents::POST_BUILD, $event);
        $config = $event->getConfig();

        if (isset($config["fields"]) && count($config["fields"]) > 1) {
            ksort($config["fields"]);
        }
    }

    /**
     * @param array $config
     * @param array $context
     * @throws \Exception
     */
    public function buildDataObjectMutations(&$config = [], $context = [])
    {
        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getMutationEntities();


        foreach ($entities as $entity) {
            $class = ClassDefinition::getByName($entity);
            if (!$class) {
                Logger::error("class " . $entity . " not found");
                continue;
            }
            $entityConfig = $configuration->getMutationEntityConfig($entity);

            $queryResolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QueryType($this->eventDispatcher, $class, $configuration);
            $queryResolver->setGraphQlService($this->getGraphQlService());

            $modelFactory = $this->modelFactory;
            $localeService = $this->localeService;

            if (isset($entityConfig["create"]) && $entityConfig["create"]) {
                // create
                $createResultType = new ObjectType([
                    'name' => 'Create' . ucfirst($entity) . "Result",
                    'fields' => [
                        'success' => ['type' => Type::boolean()],
                        'message' => ['type' => Type::string()],
                        'output' => [
                            'args' => ['defaultLanguage' => ['type' => Type::string()]],
                            'type' => \Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions::get($class),
                            'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($queryResolver) {
                                $args["id"] = $value["id"];
                                $value = $queryResolver->resolveObjectGetter($value, $args, $context, $info);
                                return $value;
                            }
                        ]
                    ],
                ]);

                $opName = 'create' . ucfirst($entity);

                $this->generateInputFieldsAndProcessors($inputFields, $processors, $context, $entity, $class);

                $inputTypeName = 'Update' . ucfirst($entity) . "Input";
                $inputType = $this->typeCache[$inputTypeName] ? $this->typeCache[$inputTypeName] : new InputObjectType([
                    'name' => $inputTypeName,
                    'fields' => $inputFields
                ]);
                $this->typeCache[$inputTypeName] = $inputType;

                $me = $this;

                $createField = [
                    'type' => $createResultType,
                    'args' => [
                        // key is not mandatory as I'll probably add a way to create a new object by fullpath
                        'key' => ['type' => Type::nonNull(Type::string())],
                        'path' => ['type' => Type::string()],
                        'parentId' => ['type' => Type::int()],
                        'published' => ['type' => Type::boolean(), 'description' => "Default is true!"],
                        'omitMandatoryCheck' => ['type' => Type::boolean()],
                        'input' => $inputType
                    ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($entity, $modelFactory, $processors, $localeService, $me) {
                        $parent = null;

                        if (isset($args["parentId"])) {
                            $parent = AbstractObject::getById($args["parentId"]);
                        } else if (isset($args["path"])) {
                            $parent = AbstractObject::getByPath($args["path"]);
                        }

                        //TODO maybe add error code?
                        if (!$parent) {
                            return [
                                "success" => false,
                                "message" => "unable to resolve parent"
                            ];
                        }

                        /** @var $configuration Configuration */
                        $configuration = $context['configuration'];
                        if (!WorkspaceHelper::isAllowed($parent, $configuration, "create") && !$me->omitPermissionCheck) {
                            return [
                                "success" => false,
                                "message" => "not allowed to create object " . $entity
                            ];
                        }

                        $published = true;
                        // default is true!
                        if (isset($args["published"])) {
                            $published = $args["published"];
                        }

                        $key = $args["key"];

                        /** @var  $newInstance Concrete */
                        $className = 'Pimcore\\Model\\DataObject\\' . ucfirst($entity);
                        $newInstance = $modelFactory->build($className);
                        $newInstance->setPublished($published);
                        $newInstance->setParent($parent);
                        $newInstance->setKey($key);

                        $resolver = $me->getUpdateObjectResolver($entity, $modelFactory, $processors, $localeService, $newInstance, $me->omitPermissionCheck);

                        call_user_func_array($resolver, [$value, $args, $context, $info]);

                        if (isset($args["omitMandatoryCheck"])) {
                            $newInstance->setOmitMandatoryCheck($args["omitMandatoryCheck"]);
                        }

                        $newInstance->save();

                        return [
                            "success" => true,
                            "message" => "object created: " . $newInstance->getId(),
                            "id" => $newInstance->getId()
                        ];
                    }
                ];

                $config['fields'][$opName] = $createField;
            }

            if (isset($entityConfig["update"]) && $entityConfig["update"]) {

                // update
                $opName = 'update' . ucfirst($entity);

                $updateResultType = new ObjectType([
                    'name' => 'Update' . ucfirst($entity) . "Result",
                    'fields' => [
                        'success' => ['type' => Type::boolean()],
                        'message' => ['type' => Type::string()],
                        'output' => [
                            'args' => ['defaultLanguage' => ['type' => Type::string()]],
                            'type' => \Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions::get($class),
                            'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($queryResolver) {
                                $args["id"] = $value["id"];
                                $value = $queryResolver->resolveObjectGetter($value, $args, $context, $info);
                                return $value;
                            }
                        ]
                    ],
                ]);

                $this->generateInputFieldsAndProcessors($inputFields, $processors, $context, $entity, $class);

                if ($inputFields) {
                    $inputTypeName = 'Update' . ucfirst($entity) . "Input";
                    $inputType = isset($this->typeCache[$inputTypeName]) ? $this->typeCache[$inputTypeName] : new InputObjectType([
                        'name' => $inputTypeName,
                        'fields' => $inputFields
                    ]);
                    $this->typeCache[$inputTypeName] = $inputType;


                    $updateField = [
                        'type' => $updateResultType,
                        'args' => [
                            'id' => ['type' => Type::nonNull(Type::int())],
                            'defaultLanguage' => ['type' => Type::string()],
                            'omitMandatoryCheck' => ['type' => Type::boolean()],
                            'input' => ['type' => $inputType],
                        ], 'resolve' => $this->getUpdateObjectResolver($entity, $modelFactory, $processors, $localeService, null, $this->omitPermissionCheck)
                    ];

                    $config['fields'][$opName] = $updateField;
                }
            }

            if (isset($entityConfig["delete"]) && $entityConfig["delete"]) {
                $opName = 'delete' . ucfirst($entity);

                $deleteResultType = new ObjectType([
                    'name' => 'Delete' . ucfirst($entity) . "Result",
                    'fields' => [
                        'success' => ['type' => Type::boolean()],
                        'message' => ['type' => Type::string()]
                    ],
                ]);

                $me = $this;
                $deleteField = [
                    'type' => $deleteResultType,
                    'args' => [
                        'id' => ['type' => Type::nonNull(Type::int())],
                    ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($entity, $modelFactory, $me) {
                        try {
                            $id = $args["id"];
                            /** @var $configuration Configuration */
                            $configuration = $context['configuration'];
                            $className = 'Pimcore\\Model\\DataObject\\' . ucfirst($entity);
                            $object = $className::getById($id);

                            if (!WorkspaceHelper::isAllowed($object, $configuration, "delete") && !$me->omitPermissionCheck) {
                                return [
                                    "success" => false,
                                    "message" => "permission denied."
                                ];
                            }
                            $object->delete();

                            return [
                                "success" => true,
                                "message" => ""
                            ];
                        } catch (\Exception $e) {
                            return [
                                "success" => false,
                                "message" => $e->getMessage()
                            ];

                        }
                    }
                ];

                $config['fields'][$opName] = $deleteField;
            }

        }
    }

    public function generateInputFieldsAndProcessors(&$inputFields, &$processors, $context, $entity, $class)
    {
        $inputFields = [];
        $processors = [];

        if ($context['clientname']) {
            /** @var $configurationItem Configuration */
            $configurationItem = $context['configuration'];

            $columns = $configurationItem->getMutationColumnConfig($entity)['columns'];

            if ($columns) {
                /** @var $fieldHelper DataObjectFieldHelper */
                $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();

                foreach ($columns as $column) {
                    $result = $fieldHelper->getMutationFieldConfigFromConfig($column, $class, $inputFields);
                    if ($result) {
                        $inputFields[$result['key']] = $result['arg'];
                        $processor = $result['processor'];
                        $processors[$result['key']] = $processor;
                    }
                }
            }
        }
    }

    /**
     * @param $entity
     * @param $modelFactory
     * @param $processors
     * @param LocaleServiceInterface $localeService
     * @param null $object
     * @param bool $omitPermissionCheck
     * @return \Closure
     */
    public function getUpdateObjectResolver($entity, $modelFactory, $processors, $localeService, $object = null, $omitPermissionCheck = false)
    {
        return static function ($value, $args, $context, $info) use ($entity, $modelFactory, $processors, $localeService, $object, $omitPermissionCheck) {
            try {

                /** @var $configuration Configuration */
                $configuration = $context['configuration'];

                if (!$object) {
                    $className = 'Pimcore\\Model\\DataObject\\' . ucfirst($entity);
                    $id = $args["id"];
                    $object = $className::getById($id);
                }

                if (!WorkspaceHelper::isAllowed($object, $configuration, "update") && !$omitPermissionCheck) {
                    return [
                        "success" => false,
                        "message" => "permission denied."
                    ];
                }

                if (isset($args['defaultLanguage'])) {
                    $localeService->setLocale($args['defaultLanguage']);
                }

                if (isset($args["omitMandatoryCheck"])) {
                    $object->setOmitMandatoryCheck($args["omitMandatoryCheck"]);
                }

                $dataIn = $args["input"];
                if (is_array($dataIn)) {
                    foreach ($dataIn as $key => $value) {
                        if (isset($processors[$key])) {
                            $processor = $processors[$key];
                            call_user_func_array($processor, [$object, $value, $args, $context, $info]);

                        }
                    }
                }

                $object->save();
            } catch (\Exception $e) {
                return [
                    "success" => false,
                    "message" => $e->getMessage()
                ];

            }

            return [
                "success" => true,
                "message" => "hurray",
                "id" => $object->getId()
            ];
        };
    }

    /**
     * @param $config
     * @param array $context
     */
    public function buildCreateAssetMutation(&$config, $context)
    {
        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (isset($entities["asset"]["create"]) && $entities["asset"]["create"]) {
            $queryResolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QueryType($this->eventDispatcher, null, $configuration);
            $queryResolver->setGraphQlService($this->getGraphQlService());
            $queryResolver = [$queryResolver, "resolveAssetGetter"];
            $service = $this->getGraphQlService();
            $assetType = $service->buildAssetType("asset");

            $createResultType = new ObjectType([
                'name' => 'CreateAssetResult',
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()],
                    "assetData" => [
                        'args' => ['defaultLanguage' => ['type' => Type::string()]],
                        'type' => $assetType,
                        'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($queryResolver) {
                            $args["id"] = $value["id"];
                            $value = $queryResolver->resolveObjectGetter($value, $args, $context, $info);

                            return $value;
                        }
                    ]
                ],
            ]);

            $opName = 'createAsset';
            $omitPermissionCheck = $this->omitPermissionCheck;

            $createField = [
                'type' => $createResultType,
                'args' => [
                    'filename' => ['type' => Type::nonNull(Type::string())],
                    'path' => ['type' => Type::string()],
                    'parentId' => ['type' => Type::int()],
                    'type' => ['type' => Type::nonNull(Type::string()), 'description' => 'image or whatever'],
                    'input' => $this->getGraphQlService()->getAssetTypeDefinition("asset_input"),
                ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($omitPermissionCheck) {
                    $parent = null;

                    if (isset($args["parentId"])) {
                        $parent = AbstractObject::getById($args["parentId"]);
                    } else if (isset($args["path"])) {
                        $parent = AbstractObject::getByPath($args["path"]);
                    }

                    //TODO maybe add error code?
                    if (!$parent) {
                        return [
                            "success" => false,
                            "message" => "unable to resolve parent"
                        ];
                    }

                    /** @var $configuration Configuration */
                    $configuration = $context['configuration'];
                    if (!WorkspaceHelper::isAllowed($parent, $configuration, "create") && !$omitPermissionCheck) {
                        return [
                            "success" => false,
                            "message" => "not allowed to create asset"
                        ];
                    }

                    $type = $args["type"];

                    /** @var  $newInstance Concrete */
                    $className = 'Pimcore\\Model\\Asset\\' . ucfirst($type);
                    $newInstance = new $className();
                    $newInstance->setParentId($parent->getId());

                    if (isset($args["input"])) {
                        $inputValues = $args["input"];
                        foreach ($inputValues as $key => $value) {
                            if ($key === "data") {
                                $value = base64_decode($value);
                            }
                            $setter = "set" . ucfirst($key);
                            $newInstance->$setter($value);
                        }
                    }
                    $newInstance->save();

                    return [
                        "success" => true,
                        "message" => "asset created: " . $newInstance->getId(),
                        "id" => $newInstance->getId()
                    ];
                }
            ];

            $config['fields'][$opName] = $createField;

        }
    }

    /**
     * @param $config
     * @param array $context
     * @throws \Exception
     */
    public function buildUpdateAssetMutation(&$config, $context)
    {
        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (isset($entities["asset"]["update"]) && $entities["asset"]["update"]) {
            $queryResolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QueryType($this->eventDispatcher, null, $configuration);
            $queryResolver->setGraphQlService($this->getGraphQlService());
            $queryResolver = [$queryResolver, "resolveAssetGetter"];
            $service = $this->getGraphQlService();
            $assetType = $service->buildAssetType("asset");

            $updateResultType = new ObjectType([
                'name' => 'UpdateAssetResult',
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()],
                    "assetData" => [
                        'args' => ['defaultLanguage' => ['type' => Type::string()]],
                        'type' => $assetType,
                        'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($queryResolver) {
                            $args["id"] = $value["id"];
                            $value = $queryResolver($value, $args, $context, $info);
                            return $value;
                        }
                    ]
                ],
            ]);

            $opName = 'updateAsset';

            $updateField = [
                'type' => $updateResultType,
                'args' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                    'input' => $this->getGraphQlService()->getAssetTypeDefinition("asset_input"),
                ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) {
                    $element = Asset::getById($args["id"]);


                    if (isset($args["input"])) {
                        $inputValues = $args["input"];
                        foreach ($inputValues as $key => $value) {
                            if ($key === "data") {
                                $value = base64_decode($value);
                            }
                            $setter = "set" . ucfirst($key);
                            $element->$setter($value);
                        }
                    }
                    $element->save();

                    return [
                        "success" => true,
                        "message" => "asset updated: " . $element->getId(),
                        "id" => $element->getId()
                    ];
                }
            ];

            $config['fields'][$opName] = $updateField;

        }
    }

    /**
     * @param $type
     * @param $config
     * @param array $context
     */
    public function buildCreateFolderMutation($type, &$config, $context)
    {
        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (isset($entities[$type . "_folder"]["create"]) && $entities[$type . "_folder"]["create"]) {
            $opName = 'create' . ucfirst($type) . "Folder";
            $createResultType = new ObjectType([
                'name' => 'Create' . ucfirst($type) . "FolderResult",
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()],
                    'id' => ['type' => Type::int()]
                ],
            ]);

            $args = [
                'path' => ['type' => Type::string()],
                'parentId' => ['type' => Type::int()]
            ];

            if ($type === "asset") {
                $args['filename'] = ['type' => Type::nonNull(Type::string())];
            } else {
                $args['key'] = ['type' => Type::nonNull(Type::string())];
            }

            $resolverFn = $this->getCreateFolderResolver($type);
            $createField = [
                'type' => $createResultType,
                'args' => $args,
                'resolve' => $resolverFn
            ];

            $config['fields'][$opName] = $createField;
        }
    }

    /**
     * @param $elementType
     * @return \Closure
     */
    public function getCreateFolderResolver($elementType)
    {
        $me = $this;
        return static function ($value, $args, $context, ResolveInfo $info) use ($elementType, $me) {
            $parent = null;


            if ($elementType == "asset") {
                if (isset($args["parentId"])) {
                    $parent = Asset::getById($args["parentId"]);
                } else if (isset($args["path"])) {
                    $parent = Asset::getByPath($args["path"]);
                }
            } else if ($elementType == "document") {
                if (isset($args["parentId"])) {
                    $parent = Document::getById($args["parentId"]);
                } else if (isset($args["path"])) {
                    $parent = Document::getByPath($args["path"]);
                }
            } else {
                if (isset($args["parentId"])) {
                    $parent = AbstractObject::getById($args["parentId"]);
                } else if (isset($args["path"])) {
                    $parent = AbstractObject::getByPath($args["path"]);
                }
            }

            if (!$parent) {
                return [
                    "success" => false,
                    "message" => "unable to resolve parent"
                ];
            }

            /** @var $configuration Configuration */
            $configuration = $context['configuration'];
            if (!WorkspaceHelper::isAllowed($parent, $configuration, "create") && !$me->omitPermissionCheck) {
                return [
                    "success" => false,
                    "message" => "not allowed to create " . $elementType . "folder "
                ];
            }

            if ($elementType === "asset") {
                $newInstance = new Folder();
                $newInstance->setFilename($args["filename"]);
            } else if ($elementType === "object") {
                $newInstance = new DataObject\Folder();
                $newInstance->setFilename($args["filename"]);
            } else if ($elementType === "document"){
                $newInstance = new Document\Folder();
                $newInstance->setKey($args["key"]);
            }

            $newInstance->setParentId($parent->getId());

            $newInstance->save();

            return [
                "success" => true,
                "message" => "folder created: " . $newInstance->getId(),
                "id" => $newInstance->getId()
            ];
        };

    }

    /**
     * @param $type
     * @param $config
     * @param array $context
     */
    public function buildUpdateFolderMutation($type, &$config, $context)
    {
        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (isset($entities[$type . "_folder"]["update"]) && $entities[$type . "_folder"]["update"]) {
            // update
            $opName = 'update' . ucfirst($type) . "Folder";

            $inputFields = [
                'parentId' => ['type' => Type::int()]
            ];
            if ($type === "asset") {
                $inputFields['filename'] = ['type' => Type::string()];
            } else {
                $inputFields['key'] = ['type' => Type::string()];
            }
            $inputType = new InputObjectType([
                'name' => 'Update' . ucfirst($type) . "FolderInput",
                'fields' => $inputFields
            ]);

            $updateResultType = new ObjectType([
                'name' => 'Update' . ucfirst($type) . "FolderResult",
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()]
                ],
            ]);

            $omitPermissionCheck = $this->omitPermissionCheck;

            $updateField = [
                'type' => $updateResultType,
                'args' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                    'input' => ['type' => $inputType],
                ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($type, $omitPermissionCheck) {
                    try {
                        $id = $args["id"];
                        /** @var $configuration Configuration */
                        $configuration = $context['configuration'];
                        if ($type === "asset") {
                            $element = Folder::getById($id);
                        } else if ($type == "document") {
                            $element = Document\Folder::getById($id);
                        } else {
                            $element = \Pimcore\Model\DataObject\Folder::getById($id);
                        }

                        if (!WorkspaceHelper::isAllowed($element, $configuration, "update") && !$omitPermissionCheck) {
                            return [
                                "success" => false,
                                "message" => "permission denied."
                            ];
                        }

                        $inputArgs = isset($args["input"]) ? $args["input"] : [];

                        foreach ($inputArgs as $argKey => $argValue) {
                            $setter = "set" . ucfirst($argKey);
                            $element->$setter($argValue);
                        }

                        $element->save();
                    } catch (\Exception $e) {
                        return [
                            "success" => false,
                            "message" => $e->getMessage()
                        ];
                    }

                    return [
                        "success" => true,
                        "message" => "hurray",
                        "id" => $element->getId()
                    ];
                }
            ];

            $config['fields'][$opName] = $updateField;
        }

    }

    /**
     * @param $config
     * @param array $context
     */
    public function buildDeleteAssetMutation(&$config, $context)
    {
        $this->buildDeleteElementMutation($config, $context, "asset");
    }

    /**
     * @param $config
     * @param array $context
     * @param $type
     */
    public function buildDeleteElementMutation(&$config, $context, $type)
    {
        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (isset($entities[$type]["delete"]) && $entities[$type]["delete"]) {
            $opName = 'delete' . ucfirst($type);

            $deleteResultType = new ObjectType([
                'name' => 'Delete' . ucfirst($type) . 'Result',
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()]
                ],
            ]);

            $omitPermissionCheck = $this->omitPermissionCheck;

            $deleteField = [
                'type' => $deleteResultType,
                'args' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($type, $omitPermissionCheck) {
                    try {
                        $id = $args["id"];
                        /** @var $configuration Configuration */
                        $configuration = $context['configuration'];
                        $element = null;

                        if ($type == "asset") {
                            $element = Asset::getById($id);
                        } else if ($type == "document") {
                            $element = Document::getById($id);
                        } else if ($type == "object") {
                            $element = DataObject::getById($id);
                        }

                        if (!WorkspaceHelper::isAllowed($element, $configuration, "delete") && !$omitPermissionCheck) {
                            return [
                                "success" => false,
                                "message" => "delete " . $type . " permission denied."
                            ];
                        }
                        $element->delete();

                        return [
                            "success" => true,
                            "message" => $type . " " . $id . " deleted"
                        ];
                    } catch (\Exception $e) {
                        return [
                            "success" => false,
                            "message" => $e->getMessage()
                        ];
                    }
                }
            ];

            $config['fields'][$opName] = $deleteField;
        }
    }

    /**
     * @param $config
     * @param array $context
     */
    public function buildDeleteDocumentMutation(&$config, $context)
    {
        $this->buildDeleteElementMutation($config, $context, "document");
    }

    /**
     * @param string $type
     * @param array $config
     * @param array $context
     */
    public function buildDeleteFolderMutation($type, &$config, $context)
    {
        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (isset($entities[$type . "_folder"]["delete"]) && $entities[$type . "_folder"]["delete"]) {
            $opName = 'delete' . ucfirst($type) . "Folder";

            $deleteResultType = new ObjectType([
                'name' => 'Delete' . ucfirst($type) . "FolderResult",
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()]
                ],
            ]);

            $omitPermissionCheck = $this->omitPermissionCheck;

            $deleteField = [
                'type' => $deleteResultType,
                'args' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($type, $omitPermissionCheck) {
                    try {
                        $id = $args["id"];
                        /** @var $configuration Configuration */
                        $configuration = $context['configuration'];

                        if ($type === "asset") {
                            $element = Folder::getById($id);
                        } else if ($type == "document") {
                            $element = Document\Folder::getById($id);
                        } else {
                            $element = \Pimcore\Model\DataObject\Folder::getById($id);
                        }

                        if (!WorkspaceHelper::isAllowed($element, $configuration, "delete") && !$omitPermissionCheck) {
                            return [
                                "success" => false,
                                "message" => "delete " . $type . " permission denied."
                            ];
                        }
                        $element->delete();

                        return [
                            "success" => true,
                            "message" => ""
                        ];
                    } catch (\Exception $e) {
                        return [
                            "success" => false,
                            "message" => $e->getMessage()
                        ];
                    }
                }
            ];

            $config['fields'][$opName] = $deleteField;
        }
    }

    /**
     * @param $elementType
     * @return \Closure
     */
    public function getUpdateFolderResolver($elementType)
    {
        $me = $this;
        return static function ($value, $args, $context, ResolveInfo $info) use ($elementType, $me) {
            $parent = null;

            if (isset($args["parentId"])) {
                $parent = AbstractObject::getById($args["parentId"]);
            } else if (isset($args["path"])) {
                $parent = AbstractObject::getByPath($args["path"]);
            }

            if (!$parent) {
                return [
                    "success" => false,
                    "message" => "unable to resolve parent"
                ];
            }

            /** @var $configuration Configuration */
            $configuration = $context['configuration'];
            if (!WorkspaceHelper::isAllowed($parent, $configuration, "update") && !$me->omitPermissionCheck) {
                return [
                    "success" => false,
                    "message" => "not allowed to create " . $elementType . "folder "
                ];
            }

            if ($elementType === "asset") {
                $newInstance = new Folder();
                $newInstance->setFilename($args["filename"]);
            } else {
                $newInstance = new \Pimcore\Model\DataObject\Folder();
                $newInstance->setKey($args["key"]);
            }
            $newInstance->setParentId($parent->getId());

            $newInstance->save();

            return [
                "success" => true,
                "message" => "folder created: " . $newInstance->getId(),
                "id" => $newInstance->getId()
            ];
        };

    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return !$this->config["fields"];
    }
}
