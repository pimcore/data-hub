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
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DataObjectFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Logger;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Folder;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Factory;

class MutationType extends ObjectType
{

    use ServiceTrait;

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
     * MutationType constructor.
     * @param Service $graphQlService
     * @param LocaleServiceInterface $localeService
     * @param Factory $modelFactory
     * @param array $config
     * @param array $context
     * @throws \Exception
     */
    public function __construct(Service $graphQlService, LocaleServiceInterface $localeService, Factory $modelFactory, $config = [], $context = [])
    {
        if (!isset($config['name'])) {
            $config['name'] = 'Mutations';
        }
        $this->setGraphQLService($graphQlService);
        $this->localeService = $localeService;
        $this->modelFactory = $modelFactory;

        $this->build($config, $context);
        parent::__construct($config);
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

            $queryResolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QueryType($class, $configuration);
            $queryResolver->setGraphQlService($this->getGraphQlService());

            if ($entityConfig["create"]) {
                // create
                $createResultType = new ObjectType([
                    'name' => 'Create' . ucfirst($entity) . "Result",
                    'fields' => [
                        'success' => ['type' => Type::boolean()],
                        'message' => ['type' => Type::string()],
                        lcfirst($class->getName()) . "Data" => [
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

                $modelFactory = $this->modelFactory;
                $localeService = $this->localeService;

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
                        if (!WorkspaceHelper::isAllowed($parent, $configuration, "create")) {
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

                        $resolver = $me->getUpdateObjectResolver($entity, $modelFactory, $processors, $localeService, $newInstance);

                        call_user_func_array($resolver, [ $newInstance, $value, $args, $context, $info]);

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

            if ($entityConfig["update"]) {

                // update
                $opName = 'update' . ucfirst($entity);

                $updateResultType = new ObjectType([
                    'name' => 'Update' . ucfirst($entity) . "Result",
                    'fields' => [
                        'success' => ['type' => Type::boolean()],
                        'message' => ['type' => Type::string()],
                        lcfirst($class->getName()) . "Data" => [
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
                    $inputType = $this->typeCache[$inputTypeName] ? $this->typeCache[$inputTypeName] : new InputObjectType([
                        'name' => $inputTypeName,
                        'fields' => $inputFields
                    ]);
                    $this->typeCache[$inputTypeName] = $inputType;


                    $updateField = [
                        'type' => $updateResultType,
                        'args' => [
                            'id' => ['type' => Type::nonNull(Type::int())],
                            'defaultLanguage' => ['type' => Type::string()],
                            'input' => ['type' => $inputType],
                        ], 'resolve' => $this->getUpdateObjectResolver($entity, $modelFactory, $processors, $localeService, null)
                    ];

                    $config['fields'][$opName] = $updateField;
                }
            }

            if ($entityConfig["delete"]) {
                $opName = 'delete' . ucfirst($entity);
                $modelFactory = $this->modelFactory;

                $deleteResultType = new ObjectType([
                    'name' => 'Delete' . ucfirst($entity) . "Result",
                    'fields' => [
                        'success' => ['type' => Type::boolean()],
                        'message' => ['type' => Type::string()]
                    ],
                ]);

                $deleteField = [
                    'type' => $deleteResultType,
                    'args' => [
                        'id' => ['type' => Type::nonNull(Type::int())],
                    ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($entity, $modelFactory) {
                        try {
                            $id = $args["id"];
                            /** @var $configuration Configuration */
                            $configuration = $context['configuration'];
                            $className = 'Pimcore\\Model\\DataObject\\' . ucfirst($entity);
                            $object = $className::getById($id);
                            if (!WorkspaceHelper::isAllowed($object, $configuration, "delete")) {
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

    public function generateInputFieldsAndProcessors(&$inputFields, &$processors, $context, $entity, $class) {
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

    public function getUpdateObjectResolver($entity, $modelFactory, $processors, $localeService, $object = null) {
        return static function ($value, $args, $context, ResolveInfo $info) use ($entity, $modelFactory, $processors, $localeService, $object) {
            try {

                /** @var $configuration Configuration */
                $configuration = $context['configuration'];

                if (!$object) {
                    $className = 'Pimcore\\Model\\DataObject\\' . ucfirst($entity);
                    $id = $args["id"];
                    $object = $className::getById($id);
                }

                if (!WorkspaceHelper::isAllowed($object, $configuration, "update")) {
                    return [
                        "success" => false,
                        "message" => "permission denied."
                    ];
                }

                if (isset($args['defaultLanguage'])) {
                    $localeService->setLocale($args['defaultLanguage']);
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
     * @param $type
     * @param $config
     * @param $context
     */
    public function buildCreateFolderMutation($type, &$config, $context)
    {
        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if ($entities[$type . "_folder"]["create"]) {
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

            if ($type == "asset") {
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
     * @param $type
     * @param $config
     * @param $context
     */
    public function buildUpdateFolderMutation($type, &$config, $context)
    {
        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if ($entities[$type . "_folder"]["update"]) {
            // update
            $opName = 'update' . ucfirst($type) . "Folder";

            $inputFields = [
                'parentId' => ['type' => Type::int()]
            ];
            if ($type == "asset") {
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

            $updateField = [
                'type' => $updateResultType,
                'args' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                    'input' => ['type' => $inputType],
                ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($type) {
                    try {
                        $id = $args["id"];
                        /** @var $configuration Configuration */
                        $configuration = $context['configuration'];
                        if ($type == "asset") {
                            $element = Folder::getById($id);
                        } else {
                            $element = \Pimcore\Model\DataObject\Folder::getById($id);
                        }

                        if (!WorkspaceHelper::isAllowed($element, $configuration, "update")) {
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
     * @param $type
     * @param $config
     * @param $context
     */
    public function buildDeleteFolderMutation($type, &$config, $context)
    {
        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if ($entities[$type . "_folder"]["delete"]) {
            $opName = 'delete' . ucfirst($type) . "Folder";

            $deleteResultType = new ObjectType([
                'name' => 'Delete' . ucfirst($type) . "FolderResult",
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()]
                ],
            ]);

            $deleteField = [
                'type' => $deleteResultType,
                'args' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($type) {
                    try {
                        $id = $args["id"];
                        /** @var $configuration Configuration */
                        $configuration = $context['configuration'];

                        if ($type == "asset") {
                            $element = Folder::getById($id);
                        } else {
                            $element = \Pimcore\Model\DataObject\Folder::getById($id);
                        }

                        if (!WorkspaceHelper::isAllowed($element, $configuration, "delete")) {
                            return [
                                "success" => false,
                                "message" => "permission denied."
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
     * @param $config
     * @param $context
     * @throws \Exception
     */
    public function buildUpdateAssetMutation(&$config, $context)
    {
        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if ($entities["asset"]["update"]) {
            $queryResolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QueryType(null, $configuration);
            $queryResolver->setGraphQlService($this->getGraphQlService());
            $queryResolver = [$queryResolver, "resolveAssetGetter"];

            $updateResultType = new ObjectType([
                'name' => 'UpdateAssetResult',
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()],
                    "assetData" => [
                        'args' => ['defaultLanguage' => ['type' => Type::string()]],
                        'type' => $this->getGraphQlService()->getTypeDefinition("asset"),
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
                    'input' => $this->getGraphQlService()->getTypeDefinition("asset_input"),
                ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) {
                    $element = Asset::getById($args["id"]);


                    if (isset($args["input"])) {
                        $inputValues = $args["input"];
                        foreach ($inputValues as $key => $value) {
                            if ($key == "data") {
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
     * @param $config
     * @param $context
     */
    public function buildCreateAssetMutation(&$config, $context)
    {
        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if ($entities["asset"]["create"]) {
            $queryResolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QueryType(null, $configuration);
            $queryResolver->setGraphQlService($this->getGraphQlService());
            $queryResolver = [$queryResolver, "resolveAssetGetter"];

            $createResultType = new ObjectType([
                'name' => 'CreateAssetResult',
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()],
                    "assetData" => [
                        'args' => ['defaultLanguage' => ['type' => Type::string()]],
                        'type' => $this->getGraphQlService()->getTypeDefinition("asset"),
                        'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($queryResolver) {
                            $args["id"] = $value["id"];
                            $value = $queryResolver->resolveObjectGetter($value, $args, $context, $info);

                            return $value;
                        }
                    ]
                ],
            ]);

            $opName = 'createAsset';

            $createField = [
                'type' => $createResultType,
                'args' => [
                    'filename' => ['type' => Type::nonNull(Type::string())],
                    'path' => ['type' => Type::string()],
                    'parentId' => ['type' => Type::int()],
                    'type'=> ['type' => Type::nonNull(Type::string()), 'description' => 'image or whatever'],
                    'input' => $this->getGraphQlService()->getTypeDefinition("asset_input"),
                ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) {
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
                    if (!WorkspaceHelper::isAllowed($parent, $configuration, "create")) {
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
                            if ($key == "data") {
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
     * @param $context
     */
    public function buildDeleteAssetMutation(&$config, $context)
    {
        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if ($entities["asset"]["delete"]) {
            $opName = 'deleteAsset';

            $deleteResultType = new ObjectType([
                'name' => 'DeleteAssetResult',
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()]
                ],
            ]);

            $deleteField = [
                'type' => $deleteResultType,
                'args' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) {
                    try {
                        $id = $args["id"];
                        /** @var $configuration Configuration */
                        $configuration = $context['configuration'];

                        $element = Asset::getById($id);

                        if (!WorkspaceHelper::isAllowed($element, $configuration, "delete")) {
                            return [
                                "success" => false,
                                "message" => "permission denied."
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
    public function getCreateFolderResolver($elementType)
    {
        return static function ($value, $args, $context, ResolveInfo $info) use ($elementType) {
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
            if (!WorkspaceHelper::isAllowed($parent, $configuration, "create")) {
                return [
                    "success" => false,
                    "message" => "not allowed to create " . $elementType . "folder "
                ];
            }

            if ($elementType == "asset") {
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
     * @param $elementType
     * @return \Closure
     */
    public function getUpdateFolderResolver($elementType)
    {
        return static function ($value, $args, $context, ResolveInfo $info) use ($elementType) {
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
            if (!WorkspaceHelper::isAllowed($parent, $configuration, "update")) {
                return [
                    "success" => false,
                    "message" => "not allowed to create " . $elementType . "folder "
                ];
            }

            if ($elementType == "asset") {
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


    /**
     * @param array $config
     * @param array $context
     *
     * @throws \Exception
     */
    public function build(&$config = [], $context = [])
    {
        $this->buildDataObjectMutations($config, $context);
        $this->buildCreateAssetMutation($config, $context);
        $this->buildUpdateAssetMutation($config, $context);
        $this->buildCreateFolderMutation("asset", $config, $context);
        $this->buildCreateFolderMutation("object", $config, $context);
        $this->buildUpdateFolderMutation("asset", $config, $context);
        $this->buildUpdateFolderMutation("object", $config, $context);
        $this->buildDeleteAssetMutation($config, $context);
        $this->buildDeleteFolderMutation("asset", $config, $context);
        $this->buildDeleteFolderMutation("object", $config, $context);
        ksort($config["fields"]);
    }
}
