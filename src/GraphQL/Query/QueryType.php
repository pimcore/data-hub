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

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\Factory;

class QueryType extends ObjectType
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


    /**
     * QueryType constructor.
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
            $config['name'] = 'Query';
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
     */
    public function buildAssetQueries(&$config = [], $context = [])
    {
        $resolver = $this->getResolver();

        // GETTER DEFINITION
        $defGet = [
            'name' => 'getAsset',
            'args' => [
                'id' => ['type' => Type::nonNull(Type::int())],
                'defaultLanguage' => ['type' => Type::string()],
            ],
            'type' => $this->getGraphQlService()->getTypeDefinition("asset"),
            'resolve' => [$resolver, "resolveAssetGetter"]
        ];

        $config['fields']['getAsset'] = $defGet;
    }

    /**
     * @param null $class
     * @param null $configuration
     * @return \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QueryType
     */
    protected function getResolver($class = null, $configuration = null) {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QueryType($class, $configuration);
        $resolver->setGraphQlService($this->getGraphQlService());
        return $resolver;
    }

    /**
     * @param array $config
     * @param array $context
     * @throws \Exception
     */
    public function buildDataObjectQueries(&$config = [], $context = [])
    {
        $listing = new ClassDefinition\Listing();
        $listing = $listing->load();

        /** @var $configuration Configuration */
        $configuration = $context['configuration'];
        $entities = $configuration->getQueryEntities();

        foreach ($listing as $class) {
            $className = $class->getName();
            if (!in_array($className, $entities)) {
                continue;
            }

            $resolver = $this->getResolver($class, $configuration);

            // GETTER DEFINITION
            $defGet = [
                'name' => 'get' . ucfirst($class->getName()),
                'args' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                    'defaultLanguage' => ['type' => Type::string()],
                ],
                'type' => \Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions::get($class),
                'resolve' => [$resolver, "resolveGetter"]
            ];

            $edgeType = new ObjectType(
                [
                    'name' => ucfirst($class->getName()) . 'Edge',
                    'fields' => [
                        'cursor' => Type::string(),
                        'node' => [
                            'type' => \Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions::get($class),
                            'resolve' => [$resolver, "resolveEdge"]
                        ],
                    ],
                ]
            );

            // LISTING DEFINITION
            $listingType = new ObjectType(
                [
                    'name' => ucfirst($class->getName()) . 'Connection',
                    'fields' => [

                        'edges' => [
                            'type' => Type::listOf($edgeType),
                            'resolve' => [$resolver, "resolveEdges"]
                        ],
                        'totalCount' => [
                            'description' => 'description for total count',
                            'resolve' => [$resolver, "resolveListingTotalCount"],
                            'type' => Type::int()
                        ]
                    ]
                ]
            );

            $defListing = [
                'name' => 'get' . ucfirst($class->getName()) . 'Listing',
                'args' => [
                    'ids' => ['type' => Type::string()],
                    'defaultLanguage' => ['type' => Type::string()],
                    'first' => ['type' => Type::int()],
                    'after' => ['type' => Type::int()],
                    'sortBy' => ['type' => Type::string()],
                    'sortOrder' => ['type' => Type::string()],
                    'filter' => ['type' => Type::string()],
                    'published' => ['type' => Type::boolean()],
                ],
                'type' => $listingType,
                'resolve' => [$resolver, "resolveListing"]
            ];

            if (!$config['fields']) {
                $config['fields'] = [];
            }


            $config['fields']['get' . ucfirst($class->getName()) . 'Listing'] = $defListing;
            $config['fields']['get' . ucfirst($class->getName())] = $defGet;
        }
    }

    /**
     * @param array $config
     * @param array $context
     *
     * @throws \Exception
     */
    public function build(&$config = [], $context = [])
    {
        $this->buildAssetQueries($config, $context);
        $this->buildDataObjectQueries($config, $context);
    }
}
