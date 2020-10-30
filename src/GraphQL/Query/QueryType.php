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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Query;

use GraphQL\Type\Definition\CustomScalarType;
use GraphQL\Type\Definition\InputObjectField;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\QueryTypeEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\QueryEvents;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\AssetListing;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\PermissionInfoTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Logger;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\Factory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class QueryType extends ObjectType
{

    use ServiceTrait;
    use PermissionInfoTrait;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var LocaleServiceInterface
     */
    protected $localeService;

    /**
     * @var Factory
     */
    protected $modelFactory;


    /**
     * QueryType constructor.
     * @param Service $graphQlService
     * @param LocaleServiceInterface $localeService
     * @param Factory $modelFactory
     * @param array $config
     * @param array $context
     * @throws \Exception
     */
    public function __construct(
        Service $graphQlService,
        LocaleServiceInterface $localeService,
        Factory $modelFactory,
        EventDispatcherInterface $eventDispatcher,
        $config = [],
        $context = []
    )
    {
        if (!isset($config['name'])) {
            $config['name'] = 'Query';
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
     */
    public function buildFolderQueries($type, &$config = [], $context = [])
    {
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (isset($entities[$type . "_folder"]["read"]) && $entities[$type . "_folder"]["read"]) {
            $resolver = $this->getResolver();

            if ($type == "asset") {
                $graphQlType = $this->getGraphQlService()->getAssetTypeDefinition("_" . $type . "_folder");
            } else {
                if ($type == "document") {
                    $graphQlType = $this->getGraphQlService()->getDocumentTypeDefinition("_" . $type . "_folder");
                } else {
                    $graphQlType = $this->getGraphQlService()->getDataObjectTypeDefinition("_" . $type . "_folder");
                }
            }

            // GETTER DEFINITION
            $defGet = [
                'name' => 'get' . ucfirst($type) . "Folder",
                'args' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                    'defaultLanguage' => ['type' => Type::string()],
                ],
                'type' => $graphQlType,
                'resolve' => [$resolver, "resolve" . ucfirst($type) . "FolderGetter"]
            ];

            $config['fields']['get' . ucfirst($type) . "Folder"] = $defGet;
        }
    }

    /**
     * @param array $config
     * @param array $context
     */
    public function buildAssetQueries(&$config = [], $context = [])
    {
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();
        $service = $this->getGraphQlService();
        $assetType = $service->buildAssetType("asset");

        if ($entities["asset"]["read"] ?? false) {
            $resolver = $this->getResolver();

            // GETTER DEFINITION
            $defGet = [
                'name' => 'getAsset',
                'args' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                    'defaultLanguage' => ['type' => Type::string()],
                ],
                'type' => $assetType,
                'resolve' => [$resolver, "resolveAssetGetter"]
            ];

            $config['fields']['getAsset'] = $defGet;
        }
    }


    /**
     * @param array $config
     * @param array $context
     */
    public function buildDocumentQueries(&$config = [], $context = [])
    {
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (isset($entities["document"]["read"]) && $entities["document"]["read"]) {
            $resolver = $this->getResolver();

            // GETTER DEFINITION
            $defGet = [
                'name' => 'getDocument',
                'args' => [
                    'id' => ['type' => Type::int()],
                    'path' => ['type' => Type::string()],
                    'defaultLanguage' => ['type' => Type::string()],
                ],
                'type' => $this->getGraphQlService()->getDocumentTypeDefinition("document"),
                'resolve' => [$resolver, "resolveDocumentGetter"]
            ];

            $config['fields']['getDocument'] = $defGet;
        }
    }


    /**
     * @param null $class
     * @param null $configuration
     * @return \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QueryType
     */
    protected function getResolver($class = null, $configuration = null)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QueryType($this->eventDispatcher, $class, $configuration, $this->omitPermissionCheck);
        $resolver->setGraphQlService($this->getGraphQlService());
        return $resolver;
    }

    /**
     * @param ClassDefinition $class
     * @param array $context
     *
     * @return \GraphQL\Type\Definition\ObjectType
     * @throws \Exception
     */
    protected function getEdgeTypeDefinition(ClassDefinition $class, array $context): ObjectType
    {
        static $instances = [];
        $configuration = $context['configuration'];
        $resolver = $this->getResolver($class, $configuration);
        $ucFirstClassName = ucfirst($class->getName());

        if (!isset($instances[$ucFirstClassName])) {
            $instances[$ucFirstClassName] = new ObjectType(
                [
                    'name' => $ucFirstClassName . 'Edge',
                    'fields' => [
                        'cursor' => Type::string(),
                        'node' => [
                            'type' => ClassTypeDefinitions::get($class),
                            'resolve' => [$resolver, "resolveEdge"]
                        ],
                    ],
                ]
            );
        }
        return $instances[$ucFirstClassName];
    }

    /**
     * @param array $config
     * @param array $context
     * @throws \Exception
     */
    public function buildDataObjectQueries(&$config = [], $context = []): void
    {
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getQueryEntities();

        foreach ($entities as $entity) {
            $class = ClassDefinition::getByName($entity);
            if (!$class) {
                Logger::error("class " . $entity . " not found");
                continue;
            }

            $resolver = $this->getResolver($class, $configuration);
            $ucFirstClassName = ucfirst($class->getName());

            // GETTER DEFINITION
            $defGet = [
                'name' => 'get' . $ucFirstClassName,
                'args' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                    'defaultLanguage' => ['type' => Type::string()],
                ],
                'type' => ClassTypeDefinitions::get($class),
                'resolve' => [$resolver, "resolveObjectGetter"],
            ];

            // LISTING DEFINITION
            $edgeType = $this->getEdgeTypeDefinition($class, $context);

            $listingType = new ObjectType(
                [
                    'name' => $ucFirstClassName . 'Connection',
                    'fields' => [

                        'edges' => [
                            'type' => Type::listOf($edgeType),
                            'resolve' => [$resolver, "resolveEdges"]
                        ],
                        'totalCount' => [
                            'description' => 'The total count of all queryable objects for this schema listing',
                            'resolve' => [$resolver, "resolveListingTotalCount"],
                            'type' => Type::int()
                        ]
                    ]
                ]
            );

            $defListing = [
                'name' => 'get' . $ucFirstClassName . 'Listing',
                'args' => [
                    'ids' => ['type' => Type::string()],
                    'defaultLanguage' => ['type' => Type::string()],
                    'first' => ['type' => Type::int()],
                    'after' => ['type' => Type::int()],
                    'sortBy' => ['type' => Type::listOf(Type::string())],
                    'sortOrder' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => "Sort by ASC or DESC, use the same position as the sortBy argument for each column to sort by",
                    ],
                    'filter' => ['type' => Type::string()],
                    'published' => ['type' => Type::boolean()],
                ],
                'type' => $listingType,
                'resolve' => [$resolver, "resolveListing"],
            ];

            if (!isset($config['fields'])) {
                $config['fields'] = [];
            }

            $config['fields']['get' . $ucFirstClassName . 'Listing'] = $defListing;
            $config['fields']['get' . $ucFirstClassName] = $defGet;
        }
    }

    /**
     * @param array &$config
     * @param array $context
     * @throws \Exception
     */
    public function buildFilterQueries(&$config = [], $context = []): void
    {
        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getQueryEntities();

        foreach ($entities as $entity) {
            $class = ClassDefinition::getByName($entity);
            if (!$class) {
                Logger::error("class " . $entity . " not found");
                continue;
            }
            if (!is_subclass_of('\\Pimcore\Model\\DataObject\\' . $class->getName(),
                \Pimcore\Bundle\EcommerceFrameworkBundle\Model\IndexableInterface::class)) {
                Logger::info("class " . $entity . " is not filterable.");
                continue;
            }

            $resolver = $this->getResolver($class, $configuration);
            $ucFirstClassName = ucfirst($class->getName());

            $edgeType = $this->getEdgeTypeDefinition($class, $context);

            $filterSelection = new ObjectType([
                'name' => $ucFirstClassName . 'FilterMultiRelation',
                'fields' => [
                    'filterType' => ['type' => Type::string()],
                    'field' => ['type' => Type::string()],
                    'label' => ['type' => Type::string()],
                    'options' => [
                        'type' => Type::listOf(new ObjectType([
                                'name' => $ucFirstClassName . 'FilterMultiRelationOption',
                                'fields' => [
                                    'value' => ['type' => Type::string()],
                                    'label' => ['type' => Type::string()],
                                    'count' => ['type' => Type::int()],
                                ],
                            ])
                        ),
                    ],
                ],
            ]);

            $filterRange = new ObjectType([
                'name' => $ucFirstClassName . 'FilterMultiNumberRange',
                'fields' => [
                    'filterType' => ['type' => Type::string()],
                    'field' => ['type' => Type::string()],
                    'label' => ['type' => Type::string()],
                    'options' => [
                        'type' => Type::listOf(new ObjectType([
                                'name' => $ucFirstClassName . 'FilterMultiNumberRangeOption',
                                'fields' => [
                                    'value' => ['type' => Type::string()],
                                    'label' => ['type' => Type::string()],
                                    'count' => ['type' => Type::int()],
                                ],
                            ])
                        ),
                    ],
                ]
            ]);

            $unionType = new UnionType([
                'name' => $ucFirstClassName . 'FilterFacet',
                'types' => [$filterSelection, $filterRange],
                'resolveType' => function ($value) use ($resolver, $filterSelection, $filterRange) {
                    if ($value['filter']->getType() == 'FilterMultiRelation') {
                        $filterSelection->resolveFieldFn = [$resolver, "resolveFacet"];
                        return $filterSelection;
                    }
                    if ($value['filter']->getType() == 'FilterMultiNumberRange') {
                        $filterRange->resolveFieldFn = [$resolver, "resolveFacet"];
                        return $filterRange;
                    }
                    throw new \Exception("No Filter Type found, only types allowed which are defined in ecommerce-config.yml, setup as ObjectType (above) and defined in Pimcore Backend Filter Definitions");
                }
            ]);

            $filterType = new ObjectType(
                [
                    'name' => $ucFirstClassName . 'Filter',
                    'fields' => [
                        'edges' => [
                            'type' => Type::listOf($edgeType),
                            'resolve' => [$resolver, "resolveEdges"]
                        ],
                        'facets' => [
                            'type' => UnionType::listOf($unionType),
                            'resolve' => [$resolver, "resolveFacets"]
                        ],
                        'totalCount' => [
                            'description' => 'The total count of all queryable objects for this schema listing',
                            'resolve' => [$resolver, "resolveFilterTotalCount"],
                            'type' => Type::int()
                        ]
                    ]
                ]
            );

            $defFilter = [
                'name' => 'get' . $ucFirstClassName . 'Filter',
                'args' => [
                    'tenant' => ['type' => Type::string()],
                    'variantMode' => [
                        'type' => Type::string(),
                        'description' => 'Define how item variants in the results are handled.. Valid values: ' .
                            \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface::VARIANT_MODE_HIDE . ',' .
                            \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface::VARIANT_MODE_INCLUDE . ',' .
                            \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface::VARIANT_MODE_INCLUDE_PARENT_OBJECT . ',' .
                            \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface::VARIANT_MODE_VARIANTS_ONLY,
                    ],
                    'defaultLanguage' => ['type' => Type::string()],
                    'fulltext' => [
                        'type' => Type::string(),
                        'description' => 'The keys to use for the fulltext search.'
                    ],
                    'first' => ['type' => Type::int()],
                    'after' => ['type' => Type::int()],
                    'sortBy' => ['type' => Type::listOf(Type::string())],
                    'sortOrder' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => "Sort by ASC or DESC, use the same position as the sortBy argument for each column to sort by",
                    ],
                    'filter' => ['type' => Type::string()],
                    'filterDefinition' => [
                        'type' => new InputObjectType([
                            'name' => $ucFirstClassName . 'FilterDefinitionArg',
                            'fields' => [
                                'id' => ['type' => Type::int()],
                                'relationField' => ['type' => Type::string()],
                                'fallbackFilterDefinitionId' => ['type' => Type::int()],
                            ],
                        ]),
                        'description' => "Define the id of a filterDefinition to use to configure the filter.",
                    ],
                    'published' => ['type' => Type::boolean()],
                    'category' => [
                        'type' => Type::id(),
                        'description' => "ID of the category to filter by.",
                    ],
                    'priceFrom' => ['type' => Type::float()],
                    'priceTo' => ['type' => Type::float()],
                    'facets' => [
                        'type' => Type::listOf(new InputObjectType([
                            'name' => $ucFirstClassName . 'FilterFacetArg',
                            'fields' => [
                                'field' => ['type' => Type::string()],
                                'values' => ['type' => new CustomScalarType([
                                    'name' => $ucFirstClassName . 'filterFacetArgValues',
                                    'description' => 'The Input can be any kind of array.
                                    For Select Filters a String Array is required e.g.
                                    "values": [
                                        "11",
                                        "12"
                                    ]
                                    For Range Filters an Object Array is required e.g.
                                    "values": [
                                        {"from": "10"},
                                        {"to": "100"}
                                    ]'
                                ])],
                                // @TODO Figure out if there's a way to use UnionType as InputObjectType.
//                                'values' => [
//                                    'type' => new UnionType([
//                                        'name' => $ucFirstClassName . 'filterFacetArgValues',
//                                        'types' => [Type::listOf(Type::string()), Type::string()]
//                                    ])
//                                ],
                            ],
                        ])),
                    ],
                ],
                'type' => $filterType,
                'resolve' => [$resolver, "resolveFilter"],
            ];

            if (!$config['fields']) {
                $config['fields'] = [];
            }
            $config['fields']['get' . $ucFirstClassName . 'Filter'] = $defFilter;
        }
    }

    /**
     * @param array $config
     * @param array $context
     * @throws \Exception
     */
    public function buildAssetListingQueries(&$config = [], $context = []): void
    {
        $configuration = $context['configuration'];
        $entities = $configuration->getSpecialEntities();

        if (!isset($entities["asset_listing"]["read"]) || !$entities["asset_listing"]["read"]) {
            return;
        }

        $listResolver = new AssetListing($this->getGraphQlService(), $this->eventDispatcher);
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Element('asset', $this->getGraphQlService(), $configuration);
        $assetTree = $this->getGraphQlService()->buildGeneralType("asset_tree");

        $edgeType = new ObjectType(
            [
                'name' => 'AssetEdge',
                'fields' => [
                    'cursor' => Type::string(),
                    'node' => [
                        'type' => $assetTree,
                        'resolve' => [$listResolver, "resolveEdge"]
                    ],
                ],
            ]
        );

        $listingType = new ObjectType(
            [
                'name' => 'AssetConnection',
                'fields' => [
                    'edges' => [
                        'type' => Type::listOf($edgeType),
                        'resolve' => [$listResolver, "resolveEdges"]
                    ],
                    'totalCount' => [
                        'description' => 'The total count of all queryable assets for this schema listing',
                        'resolve' => [$listResolver, "resolveListingTotalCount"],
                        'type' => Type::int()
                    ]
                ]
            ]
        );

        $defListing = [
            'name' => 'getAssetListing',
            'args' => [
                'ids' => ['type' => Type::string()],
                'defaultLanguage' => ['type' => Type::string()],
                'first' => ['type' => Type::int()],
                'after' => ['type' => Type::int()],
                'sortBy' => ['type' => Type::listOf(Type::string())],
                'sortOrder' => [
                    'type' => Type::listOf(Type::string()),
                    'description' => "Sort by ASC or DESC, use the same position as the sortBy argument for each column to sort by",
                ],
                'filter' => ['type' => Type::string()],
            ],
            'type' => $listingType,
            'resolve' => [$listResolver, "resolveListing"],
        ];

        $config['fields']['getAssetListing'] = $defListing;
    }

    /**
     * @param array $config
     * @param array $context
     *
     * @throws \Exception
     */
    public function build(&$config = [], $context = [])
    {
        $event = new QueryTypeEvent(
            $this,
            $config,
            $context
        );
        $this->eventDispatcher->dispatch(QueryEvents::PRE_BUILD, $event);

        $config = $event->getConfig();
        $context = $event->getContext();

        $this->buildAssetQueries($config, $context);
        $this->buildDocumentQueries($config, $context);
        $this->buildDataObjectQueries($config, $context);
        if (interface_exists('\Pimcore\Bundle\EcommerceFrameworkBundle\Model\IndexableInterface')) {
            $this->buildFilterQueries($config, $context);
        }
        $this->buildAssetListingQueries($config, $context);
        $this->buildFolderQueries("asset", $config, $context);
        $this->buildFolderQueries("document", $config, $context);
        $this->buildFolderQueries("object", $config, $context);

        $event->setConfig($config);
        $event->setContext($context);
        $this->eventDispatcher->dispatch(QueryEvents::POST_BUILD, $event);
        $config = $event->getConfig();
    }
}
