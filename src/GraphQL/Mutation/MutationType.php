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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Mutation;

use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\MutationTypeEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\MutationEvents;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementTag;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ElementIdentificationTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ElementTagTrait;
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
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\DuplicateFullPathException;
use Pimcore\Model\Element\Service as ElementService;
use Pimcore\Model\Factory;
use Pimcore\Model\Version;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MutationType extends ObjectType
{
    use ServiceTrait;

    use PermissionInfoTrait;
    use ElementIdentificationTrait;
    use ElementTagTrait;

    /** @var array */
    public static $documentElementTypes = null;

    /**
     * @var LocaleServiceInterface
     */
    protected $localeService;
    /**
     * @var Factory
     */
    protected $modelFactory;

    public static $typeCache = [];
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param Service $graphQlService
     * @param LocaleServiceInterface $localeService
     * @param Factory $modelFactory
     * @param EventDispatcherInterface $eventDispatcher
     * @param array $config
     * @param array $context
     *
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
        $config['fields'] = [];
        $event = new MutationTypeEvent(
            $this,
            $config,
            $context
        );
        $this->eventDispatcher->dispatch($event, MutationEvents::PRE_BUILD);

        $config = $event->getConfig();
        $context = $event->getContext();

        $this->buildDataObjectMutations($config, $context);
        $this->buildCreateAssetMutation($config, $context);
        $this->buildUpdateAssetMutation($config, $context);

        $this->buildUpdateDocumentMutation($config, $context, 'create', 'email');
        $this->buildUpdateDocumentMutation($config, $context, 'update', 'email');
        $this->buildUpdateDocumentMutation($config, $context, 'create', 'page');
        $this->buildUpdateDocumentMutation($config, $context, 'update', 'page');
        $this->buildUpdateDocumentMutation($config, $context, 'create', 'link');
        $this->buildUpdateDocumentMutation($config, $context, 'update', 'link');

        $this->buildCreateFolderMutation('asset', $config, $context);
        $this->buildCreateFolderMutation('object', $config, $context);
        $this->buildCreateFolderMutation('document', $config, $context);
        $this->buildUpdateFolderMutation('asset', $config, $context);
        $this->buildUpdateFolderMutation('object', $config, $context);
        $this->buildUpdateFolderMutation('document', $config, $context);
        $this->buildDeleteAssetMutation($config, $context);
        $this->buildDeleteDocumentMutation($config, $context);
        $this->buildDeleteFolderMutation('asset', $config, $context);
        $this->buildDeleteFolderMutation('document', $config, $context);
        $this->buildDeleteFolderMutation('object', $config, $context);

        $event->setConfig($config);
        $event->setContext($context);
        $this->eventDispatcher->dispatch($event, MutationEvents::POST_BUILD);
        $config = $event->getConfig();

        if (isset($config['fields']) && count($config['fields']) > 1) {
            ksort($config['fields']);
        }
    }

    /**
     * //TODO this is currently for document_pages
     *
     * @param array $config
     * @param array $context
     *
     * @throws \Exception
     */
    public function buildUpdateDocumentMutation(&$config, $context, $mutationType, $documentType)
    {
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (isset($entities['document']['update']) && $entities['document']['update']) {
            $queryResolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QueryType($this->eventDispatcher, null, $configuration);
            $queryResolver->setGraphQlService($this->getGraphQlService());

            $queryResolver = [$queryResolver, 'resolveDocumentGetter'];

            $opName = $mutationType . 'Document' . ucfirst($documentType);

            $service = $this->getGraphQlService();
            $graphQlDocumentType = $service->getDocumentTypeDefinition('document_' . $documentType);    // this is for the return stuff

            $updateResultType = new ObjectType([
                'name' => ucfirst($opName) . 'Result',
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()],
                    'document' => [
                        'args' => ['defaultLanguage' => ['type' => Type::string()]],
                        'type' => $graphQlDocumentType,
                        'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($queryResolver) {
                            if ($value['success'] === true) {
                                $args['id'] = $value['id'];
                                $value = $queryResolver($value, $args, $context, $info);
                            }

                            return $value;
                        }
                    ]
                ],
            ]);

            if ($mutationType == 'create') {
                $args = [
                    'key' => ['type' => Type::nonNull(Type::string())],
                    'path' => ['type' => Type::string()],
                    'parentId' => ['type' => Type::int()],
                    'published' => ['type' => Type::boolean(), 'description' => 'Default is true!'],
                    'userId' => ['type' => Type::int()]
                ];
            } else {
                $args = [
                    'id' => ['type' => Type::int()],
                    'fullpath' => ['type' => Type::string()],
                    'omitVersionCreate' => ['type' => Type::boolean()],
                    'userId' => ['type' => Type::int()]
                ];
            }

            $inputTypeGetter = 'getDocument' . ucfirst($documentType) . 'MutationInputType';
            $inputProcessorFn = 'processDocument' . ucfirst($documentType) . 'MutationInput';

            $processors = [];
            $inputType = $this->{$inputTypeGetter}($context, $processors);

            $inputTypeName = 'document_' . $documentType . '_input';
            self::$typeCache[$inputTypeName] = $inputType;

            $args = array_merge($args, [
                'input' => $inputType
            ]);

            $me = $this;
            $updateField = [
                'type' => $updateResultType,
                'args' => $args, 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($documentType, $inputProcessorFn, $processors, $mutationType, $me) {
                    if ($mutationType == 'update') {
                        /** @var Document $element */
                        $element = $me->getElementByTypeAndIdOrPath($args, 'document');

                        if (!WorkspaceHelper::checkPermission($element, 'update')) {
                            return [
                                'success' => false,
                                'message' => 'not allowed to update document'
                            ];
                        }
                    } else {
                        $parent = null;

                        if (isset($args['parentId'])) {
                            $parent = Document::getById($args['parentId']);
                        } elseif (isset($args['path'])) {
                            $parent = Document::getByPath($args['path']);
                        }

                        if (!$parent) {
                            return [
                                'success' => false,
                                'message' => 'unable to resolve parent'
                            ];
                        }

                        if (!WorkspaceHelper::checkPermission($parent, 'create')) {
                            return [
                                'success' => false,
                                'message' => 'not allowed to create document'
                            ];
                        }

                        $className = 'Pimcore\\Model\\Document\\' . ucfirst($documentType);
                        $factory = \Pimcore::getContainer()->get('pimcore.model.factory');
                        /** @var Document $element */
                        $element = $factory->build($className);

                        $element->setParentId($parent->getId());
                        $element->setKey($args['key']);
                        $element->setPublished($args['published'] ?? true);
                    }

                    $tags = [];
                    if (isset($args['input'])) {
                        $me->{$inputProcessorFn}($value, $args, $context, $info, $element, $processors);
                        if (isset($args['input']['tags']) && ($tag_input = $args['input']['tags'])) {
                            $tags = $me->getTagsFromInput($tag_input);
                            if (false === $tags) {
                                return [
                                    'success' => false,
                                    'message' => 'no "id" nor "path" tag data defined for tag, or tag not found',
                                ];
                            }
                        }
                    }

                    $me->saveElement($element, $args);

                    if ($tags) {
                        $me->setTags('document', $element->getId(), $tags);
                    }

                    return [
                        'success' => true,
                        'message' => 'document updated: ' . $element->getId(),
                        'id' => $element->getId()
                    ];
                }
            ];

            $config['fields'][$opName] = $updateField;
        }
    }

    /**
     * @param array $context
     * @param array $processors
     *
     * @return array
     */
    public function getDocumentEmailMutationInputType($context, &$processors = [])
    {
        $service = $this->getGraphQlService();

        $elementTypes = $service->getSupportedDocumentElementMutationDataTypes();
        $elementFields = [];
        $processors = [];
        foreach ($elementTypes as $elementType) {
            $typedef = self::$typeCache[$elementType] ?? $service->buildDocumentElementDataMutationType($elementType);
            self::$typeCache[$elementType] = $typedef;
            $elementFields[$elementType] = Type::listOf($typedef['arg']);
            $processors[$elementType] = $typedef['processor'];
        }

        $elementInputTypeList = new InputObjectType([
            'name' => 'document_emailmutationelements',
            'fields' => $elementFields
        ]);

        $inputTypeName = 'document_email_input';
        $inputType = self::$typeCache[$inputTypeName] ??
            new InputObjectType([
                'name' => $inputTypeName,
                'fields' => [
                    'key' => Type::string(),
                    'published' => Type::boolean(),
                    'module' => Type::string(),
                    'controller' => Type::string(),
                    'action' => Type::string(),
                    'template' => Type::string(),
                    'elements' => $elementInputTypeList,
                    'subject' => Type::string(),
                    'from' => Type::string(),
                    'replyTo' => Type::string(),
                    'to' => Type::string(),
                    'cc' => Type::string(),
                    'bcc' => Type::string(),
                    'tags' => ElementTag::getElementTagInputTypeDefinition(),
                ]
            ]);

        return $inputType;
    }

    /**
     * @param array $context
     * @param array $processors
     *
     * @return array
     */
    public function getDocumentLinkMutationInputType($context, &$processors = [])
    {
        $inputType = $this->getGraphQlService()->getDocumentTypeDefinition('document_link_input');

        return $inputType;
    }

    /**
     * @param ElementDescriptor|null $value
     * @param array $args
     * @param mixed $context
     * @param ResolveInfo $info
     * @param Document\Link $element
     * @param array $processors
     */
    public static function processDocumentLinkMutationInput($value, $args, $context, ResolveInfo $info, $element, $processors)
    {
        $inputValues = $args['input'];

        foreach ($inputValues as $key => $value) {
            if ($key == 'object') {
                Logger::debug('test');
                $type = $value['type'];
                $id = $value['id'];
                $target = \Pimcore\Model\Element\Service::getElementById($type, $id);
                $element->setElement($target);
            } elseif ($key == 'tags') {
                //skip it to process in callee method
            } elseif ($key == 'href') {
                $element->setDirect($value);
                $element->setLinktype('direct');
            } else {
                $setter = 'set' . ucfirst($key);

                $element->$setter($value);
            }
        }
    }

    /**
     * @param ElementDescriptor|null $value
     * @param array $args
     * @param mixed $context
     * @param ResolveInfo $info
     * @param Document\Page|Document\Email $element
     * @param array $processors
     *
     * @return void
     */
    public function processDocumentEmailMutationInput($value, $args, $context, ResolveInfo $info, $element, $processors)
    {
        self::processDocumentPageMutationInput($value, $args, $context, $info, $element, $processors);
    }

    /**
     * @param ElementDescriptor|null $value
     * @param array $args
     * @param mixed $context
     * @param ResolveInfo $info
     * @param Document\Page|Document\Email $element
     * @param array $processors
     */
    public function processDocumentPageMutationInput($value, $args, $context, ResolveInfo $info, $element, $processors)
    {
        $inputValues = $args['input'];
        foreach ($inputValues as $key => $value) {
            if ($key == 'editableUpdateStrategy') {
                if ($value == 'replaceAll') {
                    $element->setEditables([]);
                }
            } elseif ($key == 'editables') {
                $element->getEditables();

                foreach ($value as $elementType => $elementTypeValues) {
                    if ($processor = $processors[$elementType] ?? null) {
                        foreach ($elementTypeValues as $elementTypeValue) {
                            $elementTypeValue['_editableType'] = $elementType;
                            call_user_func_array($processor, [$element, $elementTypeValue, $args, $context, $info]);
                        }
                    }
                }
            } elseif ($key == 'tags') {
                //skip it to process in callee method
            } else {
                $setter = 'set' . ucfirst($key);

                $element->$setter($value);
            }
        }
    }

    /**
     * @param array $context
     * @param array $processors
     *
     * @return array
     */
    public function getDocumentPageMutationInputType($context, &$processors = [])
    {
        $service = $this->getGraphQlService();

        $elementTypes = $service->getSupportedDocumentElementMutationDataTypes();
        $elementFields = [];
        $processors = [];
        foreach ($elementTypes as $elementType) {
            $typedef = self::$typeCache[$elementType] ?? $service->buildDocumentElementDataMutationType($elementType);
            self::$typeCache[$elementType] = $typedef;
            $elementFields[$elementType] = Type::listOf($typedef['arg']);
            $processors[$elementType] = $typedef['processor'];
        }

        $elementInputTypeList = self::$typeCache['document_pagemutationelements'] ?? null;
        if (!$elementInputTypeList) {
            $elementInputTypeList = new InputObjectType([
                'name' => 'document_pagemutationelements',
                'fields' => $elementFields,
            ]);

            self::$typeCache['document_pagemutationelements'] = $elementInputTypeList;
            self::$documentElementTypes = $elementInputTypeList;
        }

        if (!isset(self::$typeCache['overwrite_strategy'])) {
            self::$typeCache['overwrite_strategy'] = new EnumType([
                'name' => 'overwrite_strategy',
                'values' => [
                    'overwrite',
                    'update'
                ]
            ]);
        }

        $inputTypeName = 'document_page_input';
        $inputType = self::$typeCache[$inputTypeName] ??
            new InputObjectType([
                'name' => $inputTypeName,
                'fields' => [
                    'key' => Type::string(),
                    'published' => Type::boolean(),
                    'module' => Type::string(),
                    'controller' => Type::string(),
                    'action' => Type::string(),
                    'template' => Type::string(),
                    'editableUpdateStrategy' => self::$typeCache['overwrite_strategy'],
                    'editables' => $elementInputTypeList,
                    'tags' => ElementTag::getElementTagInputTypeDefinition(),
                ]
            ]);

        return $inputType;
    }

    /**
     * @param array $config
     * @param array $context
     *
     * @throws \Exception
     */
    public function buildDataObjectMutations(&$config = [], $context = [])
    {
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getMutationEntities();

        foreach ($entities as $entity) {
            $class = ClassDefinition::getByName($entity);
            if (!$class) {
                Logger::error('class ' . $entity . ' not found');
                continue;
            }
            $entityConfig = $configuration->getMutationEntityConfig($entity);

            $queryResolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QueryType($this->eventDispatcher, $class, $configuration);
            $queryResolver->setGraphQlService($this->getGraphQlService());

            $modelFactory = $this->modelFactory;
            $localeService = $this->localeService;

            if (isset($entityConfig['create']) && $entityConfig['create']) {
                // create
                $createResultType = new ObjectType([
                    'name' => 'Create' . ucfirst($entity) . 'Result',
                    'fields' => [
                        'success' => ['type' => Type::boolean()],
                        'message' => ['type' => Type::string()],
                        'output' => [
                            'args' => ['defaultLanguage' => ['type' => Type::string()]],
                            'type' => \Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions::get($class),
                            'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($queryResolver) {
                                if ($value['success'] === true) {
                                    $args['id'] = $value['id'];
                                    $value = $queryResolver->resolveObjectGetter($value, $args, $context, $info);
                                }

                                return $value;
                            }
                        ]
                    ],
                ]);

                $opName = 'create' . ucfirst($entity);

                $this->generateInputFieldsAndProcessors($inputFields, $processors, $context, $entity, $class);

                $inputFields['tags'] = ElementTag::getElementTagInputTypeDefinition();

                $inputTypeName = 'Update' . ucfirst($entity) . 'Input';
                $inputType = self::$typeCache[$inputTypeName] ?? new InputObjectType([
                        'name' => $inputTypeName,
                        'fields' => $inputFields
                    ]);
                self::$typeCache[$inputTypeName] = $inputType;

                $me = $this;

                $createField = [
                    'type' => $createResultType,
                    'args' => [
                        // key is not mandatory as I'll probably add a way to create a new object by fullpath
                        'key' => ['type' => Type::nonNull(Type::string())],
                        'path' => ['type' => Type::string()],
                        'parentId' => ['type' => Type::int()],
                        'defaultLanguage' => ['type' => Type::string()],
                        'published' => ['type' => Type::boolean(), 'description' => 'Default is true!'],
                        'omitMandatoryCheck' => ['type' => Type::boolean()],
                        'userId' => ['type' => Type::int()],
                        'type' => ['type' => Type::string()],
                        'input' => $inputType,
                    ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($entity, $modelFactory, $processors, $localeService, $me) {
                        $parent = null;

                        if (isset($args['parentId'])) {
                            $parent = DataObject::getById($args['parentId']);
                        } elseif (isset($args['path'])) {
                            $parent = DataObject::getByPath($args['path']);
                        }

                        //TODO maybe add error code?
                        if (!$parent) {
                            return [
                                'success' => false,
                                'message' => 'unable to resolve parent'
                            ];
                        }

                        /** @var Configuration $configuration */
                        $configuration = $context['configuration'];
                        if (!$me->omitPermissionCheck && !WorkspaceHelper::checkPermission($parent, 'create')) {
                            return [
                                'success' => false,
                                'message' => 'not allowed to create object ' . $entity
                            ];
                        }

                        $published = true;
                        // default is true!
                        if (isset($args['published'])) {
                            $published = $args['published'];
                        }

                        $key = $args['key'];
                        $key = DataObject\Service::getValidKey($key, 'object');

                        $className = 'Pimcore\\Model\\DataObject\\' . ucfirst($entity);
                        /** @var Concrete $newInstance */
                        $newInstance = $modelFactory->build($className);
                        $newInstance->setPublished($published);
                        $newInstance->setParent($parent);
                        $newInstance->setKey($key);

                        if (isset($args['type']) && ($args['type'] == 'object' || $args['type'] == 'variant')) {
                            $newInstance->setType($args['type']);
                        }

                        $resolver = $me->getUpdateObjectResolver($processors, $localeService, $newInstance, $me->omitPermissionCheck);

                        $returnValue = call_user_func_array($resolver, [$value, $args, $context, $info]);
                        if (isset($returnValue['success']) === true &&
                            $returnValue['success'] === false) {
                            return $returnValue;
                        }

                        if (isset($args['omitMandatoryCheck'])) {
                            $newInstance->setOmitMandatoryCheck($args['omitMandatoryCheck']);
                        }

                        $tags = [];
                        if (isset($args['input'])) {
                            $inputValues = $args['input'];
                            foreach ($inputValues as $key => $value) {
                                //TODO: ask pimcore/pimcore to implement something like Asset::setTags
                                if ($key == 'tags') {
                                    $tags = $me->getTagsFromInput($value);
                                    if (false === $tags) {
                                        return [
                                            'success' => false,
                                            'message' => 'no "id" nor "path" tag data defined for tag, or tag not found',
                                        ];
                                    }
                                }
                            }
                        }

                        $me->saveElement($newInstance, $args);

                        if ($tags) {
                            $me->setTags('object', $newInstance->getId(), $tags);
                        }

                        return [
                            'success' => true,
                            'message' => 'object created: ' . $newInstance->getId(),
                            'id' => $newInstance->getId()
                        ];
                    }
                ];

                $config['fields'][$opName] = $createField;
            }

            if (isset($entityConfig['update']) && $entityConfig['update']) {

                // update
                $opName = 'update' . ucfirst($entity);

                $updateResultType = new ObjectType([
                    'name' => 'Update' . ucfirst($entity) . 'Result',
                    'fields' => [
                        'success' => ['type' => Type::boolean()],
                        'message' => ['type' => Type::string()],
                        'output' => [
                            'args' => ['defaultLanguage' => ['type' => Type::string()]],
                            'type' => \Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions::get($class),
                            'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($queryResolver) {
                                if ($value['success'] === true) {
                                    $args['id'] = $value['id'];
                                    $value = $queryResolver->resolveObjectGetter($value, $args, $context, $info);
                                }

                                return $value;
                            }
                        ]
                    ],
                ]);

                $this->generateInputFieldsAndProcessors($inputFields, $processors, $context, $entity, $class);

                $inputFields['tags'] = ElementTag::getElementTagInputTypeDefinition();

                $inputTypeName = 'Update' . ucfirst($entity) . 'Input';
                $inputType = isset(self::$typeCache[$inputTypeName]) ? self::$typeCache[$inputTypeName] : new InputObjectType([
                    'name' => $inputTypeName,
                    'fields' => $inputFields
                ]);
                self::$typeCache[$inputTypeName] = $inputType;

                $updateField = [
                    'type' => $updateResultType,
                    'args' => [
                        'id' => ['type' => Type::int()],
                        'fullpath' => ['type' => Type::string()],
                        'parentId' => ['type' => Type::int()],
                        'defaultLanguage' => ['type' => Type::string()],
                        'omitMandatoryCheck' => ['type' => Type::boolean()],
                        'omitVersionCreate' => ['type' => Type::boolean()],
                        'userId' => ['type' => Type::int()],
                        'input' => ['type' => $inputType],
                    ], 'resolve' => $this->getUpdateObjectResolver($processors, $localeService, null, $this->omitPermissionCheck)
                ];

                $config['fields'][$opName] = $updateField;
            }

            if (isset($entityConfig['delete']) && $entityConfig['delete']) {
                $opName = 'delete' . ucfirst($entity);

                $deleteResultType = new ObjectType([
                    'name' => 'Delete' . ucfirst($entity) . 'Result',
                    'fields' => [
                        'success' => ['type' => Type::boolean()],
                        'message' => ['type' => Type::string()]
                    ],
                ]);

                $me = $this;
                $deleteField = [
                    'type' => $deleteResultType,
                    'args' => [
                        'id' => ['type' => Type::int()],
                        'fullpath' => ['type' => Type::string()],
                    ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($me) {
                        try {
                            /** @var Configuration $configuration */
                            $configuration = $context['configuration'];

                            $object = $me->getElementByTypeAndIdOrPath($args, 'object');

                            if (!$object) {
                                return [
                                    'success' => false,
                                    'message' => 'unable to delete object. Unknown id or fullpath'
                                ];
                            }

                            if (!$me->omitPermissionCheck && !WorkspaceHelper::checkPermission($object, 'delete')) {
                                return [
                                    'success' => false,
                                    'message' => 'permission denied.'
                                ];
                            }
                            $object->delete();

                            return [
                                'success' => true,
                                'message' => ''
                            ];
                        } catch (\Exception $e) {
                            return [
                                'success' => false,
                                'message' => $e->getMessage()
                            ];
                        }
                    }
                ];

                $config['fields'][$opName] = $deleteField;
            }
        }
    }

    /**
     * @param array $inputFields
     * @param array $processors
     * @param array $context
     * @param string $entity
     * @param ClassDefinition|\Pimcore\Model\DataObject\Fieldcollection\Definition $class
     *
     * @return void
     */
    public function generateInputFieldsAndProcessors(&$inputFields, &$processors, $context, $entity, $class)
    {
        $inputFields = [];
        $processors = [];

        if ($context['clientname']) {
            /** @var Configuration $configurationItem */
            $configurationItem = $context['configuration'];

            $columns = $configurationItem->getMutationColumnConfig($entity)['columns'] ?? [];

            if ($columns) {
                $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();

                foreach ($columns as $column) {
                    $result = $fieldHelper->getMutationFieldConfigFromConfig($column, $class);
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
     * @param array $processors
     * @param LocaleServiceInterface $localeService
     * @param object|null $object
     * @param bool $omitPermissionCheck
     *
     * @return callable(mixed $value, array $args, array $context, ResolveInfo $info): mixed
     */
    public function getUpdateObjectResolver($processors, $localeService, $object = null, $omitPermissionCheck = false)
    {
        $me = $this;

        return static function ($value, $args, $context, $info) use ($processors, $localeService, $object, $omitPermissionCheck, $me) {
            try {
                if (!$object) {
                    $object = $me->getElementByTypeAndIdOrPath($args, 'object');

                    $parent = null;
                    if (isset($args['parentId'])) {
                        $parent = DataObject::getById($args['parentId']);
                    } elseif (isset($args['path'])) {
                        $parent = DataObject::getByPath($args['path']);
                    }

                    if ($parent) {
                        $object->setParent($parent);
                    }
                }

                if (!$object) {
                    return [
                        'success' => false,
                        'message' => 'unable to update object. Unknown id or fullpath'
                    ];
                }

                if (!$omitPermissionCheck && !WorkspaceHelper::checkPermission($object, 'update')) {
                    return [
                        'success' => false,
                        'message' => 'permission denied.'
                    ];
                }

                if (isset($args['defaultLanguage'])) {
                    $localeService->setLocale($args['defaultLanguage']);
                }

                if (isset($args['omitMandatoryCheck'])) {
                    $object->setOmitMandatoryCheck($args['omitMandatoryCheck']);
                }

                $tags = [];
                if (isset($args['input'])) {
                    $dataIn = $args['input'];
                    if (is_array($dataIn)) {
                        foreach ($dataIn as $key => $value) {
                            if (isset($processors[$key])) {
                                $processor = $processors[$key];
                                call_user_func_array($processor, [$object, $value, $args, $context, $info]);
                            } elseif ($key === 'tags') {
                                $tags = $me->getTagsFromInput($value);
                                if (false === $tags) {
                                    return [
                                        'success' => false,
                                        'message' => 'no "id" nor "path" tag data defined for tag, or tag not found',
                                    ];
                                }
                            }
                        }
                    }
                }

                $me->saveElement($object, $args);

                if ($tags) {
                    $me->setTags('object', $object->getId(), $tags);
                }
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }

            return [
                'success' => true,
                'message' => 'object ' . $object->getId() . ' updated',
                'id' => $object->getId()
            ];
        };
    }

    /**
     * @param array $config
     * @param array $context
     */
    public function buildCreateAssetMutation(&$config, $context)
    {
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (isset($entities['asset']['create']) && $entities['asset']['create']) {
            $queryResolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QueryType($this->eventDispatcher, null, $configuration);
            $queryResolver->setGraphQlService($this->getGraphQlService());
            $queryResolver = [$queryResolver, 'resolveAssetGetter'];
            $service = $this->getGraphQlService();
            $assetType = $service->buildAssetType('asset');

            $createResultType = new ObjectType([
                'name' => 'CreateAssetResult',
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()],
                    'assetData' => [
                        'args' => ['defaultLanguage' => ['type' => Type::string()]],
                        'type' => $assetType,
                        'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($queryResolver) {
                            if ($args['id'] = $value['id'] ?? null) {
                                $value = $queryResolver($value, $args, $context, $info);
                            }

                            return $value;
                        }
                    ]
                ],
            ]);

            $opName = 'createAsset';
            $omitPermissionCheck = $this->omitPermissionCheck;

            $me = $this;
            $createField = [
                'type' => $createResultType,
                'args' => [
                    'filename' => ['type' => Type::nonNull(Type::string())],
                    'path' => ['type' => Type::string()],
                    'parentId' => ['type' => Type::int()],
                    'type' => ['type' => Type::nonNull(Type::string()), 'description' => 'image or whatever'],
                    'userId' => ['type' => Type::int()],
                    'input' => $this->getGraphQlService()->getAssetTypeDefinition('asset_input'),
                ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($omitPermissionCheck, $me) {
                    $parent = null;

                    if (isset($args['parentId'])) {
                        $parent = Asset::getById($args['parentId']);
                    } elseif (isset($args['path'])) {
                        $parent = Asset::getByPath($args['path']);
                    }

                    //TODO maybe add error code?
                    if (!$parent) {
                        return [
                            'success' => false,
                            'message' => 'unable to resolve parent'
                        ];
                    }

                    if (!$omitPermissionCheck && !WorkspaceHelper::checkPermission($parent, 'create')) {
                        return [
                            'success' => false,
                            'message' => 'not allowed to create asset'
                        ];
                    }

                    $type = $args['type'];
                    $filename = $args['filename'];

                    $className = 'Pimcore\\Model\\Asset\\' . ucfirst($type);
                    /** @var Asset $newInstance */
                    $newInstance = new $className();
                    $newInstance->setParentId($parent->getId());
                    $newInstance->setFilename($filename);

                    $tags = [];
                    if (isset($args['input'])) {
                        $inputValues = $args['input'];
                        foreach ($inputValues as $key => $value) {
                            //TODO: ask pimcore/pimcore to implement something like Asset::setTags
                            if ($key == 'tags') {
                                $tags = $me->getTagsFromInput($value);
                                if (false === $tags) {
                                    return [
                                        'success' => false,
                                        'message' => 'no "id" nor "path" tag data defined for tag, or tag not found',
                                    ];
                                }
                            } else {
                                if ($key === 'data') {
                                    $value = base64_decode($value);
                                }
                                $setter = 'set' . ucfirst($key);
                                $newInstance->$setter($value);
                            }
                        }
                    }

                    try {
                        $me->saveElement($newInstance, $args);
                    } catch (DuplicateFullPathException $e) {
                        return [
                            'success' => false,
                            'message' => 'saving failed: Duplicate path'
                        ];
                    } catch (\Exception $e) {
                        return [
                            'success' => false,
                            'message' => 'saving failed: ' . $e->getMessage()
                        ];
                    }

                    if ($tags) {
                        $me->setTags('asset', $newInstance->getId(), $tags);
                    }

                    return [
                        'success' => true,
                        'message' => 'asset created: ' . $newInstance->getId(),
                        'id' => $newInstance->getId()
                    ];
                }
            ];

            $config['fields'][$opName] = $createField;
        }
    }

    /**
     * @param array $config
     * @param array $context
     *
     * @throws \Exception
     */
    public function buildUpdateAssetMutation(&$config, $context)
    {
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (isset($entities['asset']['update']) && $entities['asset']['update']) {
            $queryResolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QueryType($this->eventDispatcher, null, $configuration);
            $queryResolver->setGraphQlService($this->getGraphQlService());
            $queryResolver = [$queryResolver, 'resolveAssetGetter'];
            $service = $this->getGraphQlService();
            $assetType = $service->buildAssetType('asset');

            $updateResultType = new ObjectType([
                'name' => 'UpdateAssetResult',
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()],
                    'assetData' => [
                        'args' => ['defaultLanguage' => ['type' => Type::string()]],
                        'type' => $assetType,
                        'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($queryResolver) {
                            if ($value['success'] === true) {
                                $args['id'] = $value['id'];
                                $value = $queryResolver($value, $args, $context, $info);
                            }

                            return $value;
                        }
                    ]
                ],
            ]);

            $opName = 'updateAsset';

            $me = $this;
            $updateField = [
                'type' => $updateResultType,
                'args' => [
                    'id' => ['type' => Type::int()],
                    'fullpath' => ['type' => Type::string()],
                    'omitVersionCreate' => ['type' => Type::boolean()],
                    'userId' => ['type' => Type::int()],
                    'input' => $this->getGraphQlService()->getAssetTypeDefinition('asset_input')
                ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($me) {
                    /** @var Asset $element */
                    $element = $me->getElementByTypeAndIdOrPath($args, 'asset');
                    $tags = [];

                    if (isset($args['input'])) {
                        $inputValues = $args['input'];
                        foreach ($inputValues as $key => $value) {
                            //TODO: ask pimcore/pimcore to implement something like Asset::setTags
                            if ($key == 'tags') {
                                $tags = $me->getTagsFromInput($value);
                                if (false === $tags) {
                                    return [
                                        'success' => false,
                                        'message' => 'no "id" nor "path" tag data defined for tag, or tag not found',
                                    ];
                                }
                            } else {
                                if ($key === 'data') {
                                    $value = base64_decode($value);
                                }
                                $setter = 'set' . ucfirst($key);
                                $element->$setter($value);
                            }
                        }
                    }

                    $me->saveElement($element, $args);

                    if ($tags) {
                        $me->setTags('asset', $element->getId(), $tags);
                    }

                    return [
                        'success' => true,
                        'message' => 'asset updated: ' . $element->getId(),
                        'id' => $element->getId()
                    ];
                }
            ];

            $config['fields'][$opName] = $updateField;
        }
    }

    /**
     * @param string $type
     * @param array $config
     * @param array $context
     */
    public function buildCreateFolderMutation($type, &$config, $context)
    {
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (isset($entities[$type . '_folder']['create']) && $entities[$type . '_folder']['create']) {
            $opName = 'create' . ucfirst($type) . 'Folder';
            $createResultType = new ObjectType([
                'name' => 'Create' . ucfirst($type) . 'FolderResult',
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()],
                    'id' => ['type' => Type::int()]
                ],
            ]);

            $args = [
                'path' => ['type' => Type::string()],
                'parentId' => ['type' => Type::int()],
                'userId' => ['type' => Type::int()]
            ];

            if ($type === 'asset') {
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
     * @param string $elementType
     *
     * @return callable(mixed $value, array $args, array $context, ResolveInfo $info): mixed
     */
    public function getCreateFolderResolver($elementType)
    {
        $me = $this;

        return static function ($value, $args, $context, ResolveInfo $info) use ($elementType, $me) {
            $parent = null;

            if (isset($args['parentId'])) {
                $parent = ElementService::getElementById($elementType, $args['parentId']);
            } elseif (isset($args['path'])) {
                $parent = ElementService::getElementByPath($elementType, $args['path']);
            }

            if (!$parent) {
                return [
                    'success' => false,
                    'message' => 'unable to resolve parent'
                ];
            }

            if (!$me->omitPermissionCheck && !WorkspaceHelper::checkPermission($parent, 'create')) {
                return [
                    'success' => false,
                    'message' => 'not allowed to create ' . $elementType . 'folder '
                ];
            }

            if ($elementType === 'asset') {
                $newInstance = new Folder();
                $newInstance->setFilename($args['filename']);
            } elseif ($elementType === 'object') {
                $newInstance = new DataObject\Folder();
                $newInstance->setKey($args['key']);
            } elseif ($elementType === 'document') {
                $newInstance = new Document\Folder();
                $newInstance->setKey($args['key']);
            } else {
                throw new \Exception('ElementType not supported: ' . $elementType);
            }

            $newInstance->setParentId($parent->getId());

            if (isset($args['userId'])) {
                $newInstance->setUserOwner($args['userId']);
                $newInstance->setUserModification($args['userId']);
            }

            $newInstance->save();

            return [
                'success' => true,
                'message' => 'folder created: ' . $newInstance->getId(),
                'id' => $newInstance->getId()
            ];
        };
    }

    /**
     * @param string $type
     * @param array $config
     * @param array $context
     */
    public function buildUpdateFolderMutation($type, &$config, $context)
    {
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (isset($entities[$type . '_folder']['update']) && $entities[$type . '_folder']['update']) {
            // update
            $opName = 'update' . ucfirst($type) . 'Folder';

            $inputFields = [
                'parentId' => ['type' => Type::int()]
            ];
            if ($type === 'asset') {
                $inputFields['filename'] = ['type' => Type::string()];
            } else {
                $inputFields['key'] = ['type' => Type::string()];
            }
            $inputType = new InputObjectType([
                'name' => 'Update' . ucfirst($type) . 'FolderInput',
                'fields' => $inputFields
            ]);

            $updateResultType = new ObjectType([
                'name' => 'Update' . ucfirst($type) . 'FolderResult',
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()]
                ],
            ]);

            $omitPermissionCheck = $this->omitPermissionCheck;

            $me = $this;
            $updateField = [
                'type' => $updateResultType,
                'args' => [
                    'id' => ['type' => Type::int()],
                    'fullpath' => ['type' => Type::string()],
                    'userId' => ['type' => Type::int()],
                    'input' => ['type' => $inputType]
                ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($type, $omitPermissionCheck, $me) {
                    try {
                        /** @var Configuration $configuration */
                        $configuration = $context['configuration'];
                        $element = $me->getElementByTypeAndIdOrPath($args, $type);

                        if (!$omitPermissionCheck && !WorkspaceHelper::checkPermission($element, 'update')) {
                            return [
                                'success' => false,
                                'message' => 'permission denied.'
                            ];
                        }

                        $inputArgs = isset($args['input']) ? $args['input'] : [];

                        foreach ($inputArgs as $argKey => $argValue) {
                            $setter = 'set' . ucfirst($argKey);
                            $element->$setter($argValue);
                        }

                        if (isset($args['userId'])) {
                            $element->setUserModification($args['userId']);
                        }

                        $element->save();
                    } catch (\Exception $e) {
                        return [
                            'success' => false,
                            'message' => $e->getMessage()
                        ];
                    }

                    return [
                        'success' => true,
                        'message' => 'hurray',
                        'id' => $element->getId()
                    ];
                }
            ];

            $config['fields'][$opName] = $updateField;
        }
    }

    /**
     * @param array $config
     * @param array $context
     */
    public function buildDeleteAssetMutation(&$config, $context)
    {
        $this->buildDeleteElementMutation($config, $context, 'asset');
    }

    /**
     * @param array $config
     * @param array $context
     * @param string $type
     */
    public function buildDeleteElementMutation(&$config, $context, $type)
    {
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (isset($entities[$type]['delete']) && $entities[$type]['delete']) {
            $opName = 'delete' . ucfirst($type);

            $deleteResultType = new ObjectType([
                'name' => 'Delete' . ucfirst($type) . 'Result',
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()]
                ],
            ]);

            $omitPermissionCheck = $this->omitPermissionCheck;

            $me = $this;
            $deleteField = [
                'type' => $deleteResultType,
                'args' => [
                    'id' => ['type' => Type::int()],
                    'fullpath' => ['type' => Type::string()],
                ],
                'resolve' => static function ($value, $args) use ($type, $omitPermissionCheck, $me) {
                    try {
                        $idOrPath = $args['id'] ?? ($args['fullpath'] ?? null);
                        if (!$idOrPath) {
                            return [
                                    'success' => false,
                                    'message' => 'Missing required field id or fullpath to delete the asset.'
                                ];
                        }

                        $element = $me->getElementByTypeAndIdOrPath($args, $type);

                        if (!$omitPermissionCheck && !WorkspaceHelper::checkPermission($element, 'delete')) {
                            return [
                                    'success' => false,
                                    'message' => 'delete ' . $type . ' permission denied.'
                                ];
                        }
                        $result = ['success' => false];
                        $element->delete();

                        $result = [
                                'success' => true,
                                'message' => $type . ' ' . $idOrPath . ' deleted'
                            ];
                    } catch (\Exception $e) {
                        $result['message'] = $e->getMessage();
                    }

                    return $result;
                }
            ];

            $config['fields'][$opName] = $deleteField;
        }
    }

    /**
     * @param array $config
     * @param array $context
     */
    public function buildDeleteDocumentMutation(&$config, $context)
    {
        $this->buildDeleteElementMutation($config, $context, 'document');
    }

    /**
     * @param string $type
     * @param array $config
     * @param array $context
     */
    public function buildDeleteFolderMutation($type, &$config, $context)
    {
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (isset($entities[$type . '_folder']['delete']) && $entities[$type . '_folder']['delete']) {
            $opName = 'delete' . ucfirst($type) . 'Folder';

            $deleteResultType = new ObjectType([
                'name' => 'Delete' . ucfirst($type) . 'FolderResult',
                'fields' => [
                    'success' => ['type' => Type::boolean()],
                    'message' => ['type' => Type::string()]
                ],
            ]);

            $omitPermissionCheck = $this->omitPermissionCheck;

            $me = $this;
            $deleteField = [
                'type' => $deleteResultType,
                'args' => [
                    'id' => ['type' => Type::int()],
                    'fullpath' => ['type' => Type::string()],
                ], 'resolve' => static function ($value, $args, $context, ResolveInfo $info) use ($type, $omitPermissionCheck, $me) {
                    try {
                        $id = $args['id'];
                        /** @var Configuration $configuration */
                        $configuration = $context['configuration'];
                        $element = $me->getElementByTypeAndIdOrPath($args, $type);

                        if (!$omitPermissionCheck && !WorkspaceHelper::checkPermission($element, 'delete')) {
                            return [
                                'success' => false,
                                'message' => 'delete ' . $type . ' permission denied.'
                            ];
                        }
                        $element->delete();

                        return [
                            'success' => true,
                            'message' => ''
                        ];
                    } catch (\Exception $e) {
                        return [
                            'success' => false,
                            'message' => $e->getMessage()
                        ];
                    }
                }
            ];

            $config['fields'][$opName] = $deleteField;
        }
    }

    /**
     * @param string $elementType
     *
     * @return callable(mixed $value, array $args, array $context, ResolveInfo $info): mixed
     */
    public function getUpdateFolderResolver($elementType)
    {
        $me = $this;

        return static function ($value, $args, $context, ResolveInfo $info) use ($elementType, $me) {
            $parent = null;

            if (isset($args['parentId'])) {
                $parent = AbstractObject::getById($args['parentId']);
            } elseif (isset($args['path'])) {
                $parent = AbstractObject::getByPath($args['path']);
            }

            if (!$parent) {
                return [
                    'success' => false,
                    'message' => 'unable to resolve parent'
                ];
            }

            if (!$me->omitPermissionCheck && !WorkspaceHelper::checkPermission($parent, 'update')) {
                return [
                    'success' => false,
                    'message' => 'not allowed to create ' . $elementType . 'folder '
                ];
            }

            if ($elementType === 'asset') {
                $newInstance = new Folder();
                $newInstance->setFilename($args['filename']);
            } else {
                $newInstance = new \Pimcore\Model\DataObject\Folder();
                $newInstance->setKey($args['key']);
            }
            $newInstance->setParentId($parent->getId());

            $newInstance->save();

            return [
                'success' => true,
                'message' => 'folder created: ' . $newInstance->getId(),
                'id' => $newInstance->getId()
            ];
        };
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return !$this->config['fields'];
    }

    /**
     * @param AbstractElement|Asset|DataObject|Document $element
     * @param array $options
     */
    protected function saveElement($element, $options): void
    {
        if (
            isset($options['userId'])
            && empty($element->getId())
            && method_exists($element, 'setUserOwner')
        ) {
            $element->setUserOwner($options['userId']);
        }

        if (
            isset($options['userId'])
            && method_exists($element, 'setUserModification')
        ) {
            $element->setUserModification($options['userId']);
        }

        $omitVersionCreateBefore = Version::$disabled;

        if (isset($options['omitVersionCreate']) && $options['omitVersionCreate']) {
            Version::disable();
        }

        $element->save();

        if (isset($options['omitVersionCreate']) && $options['omitVersionCreate'] && !$omitVersionCreateBefore) {
            Version::enable();
        }
    }
}
