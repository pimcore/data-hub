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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class UrlSlugType extends ObjectType
{
    use ServiceTrait;

    /** @var Data */
    protected $fieldDefinition;

    /**
     * @param Service $graphQlService
     * @param Data|null $fieldDefinition
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, Data $fieldDefinition = null, $config = [], $context = [])
    {
        $this->fieldDefinition = $fieldDefinition;
        $this->setGraphQLService($graphQlService);
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $config['fields'] = self::getFieldConfig($this->getGraphQlService());
    }

    /**
     * @param Service $graphQlService
     *
     * @return array[]
     */
    public static function getFieldConfig(Service $graphQlService)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\UrlSlug();
        $resolver->setGraphQLService($graphQlService);
        $fields = [
            'slug' => [
                'type' => Type::string(),
                'resolve' => [$resolver, 'resolveSlug']
            ],
            'siteId' => [
                'type' => Type::int(),
                'resolve' => [$resolver, 'resolveSiteId']
            ]
        ];

        return $fields;
    }
}
