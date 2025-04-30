<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\ClassDefinition\Data;

/**
 * @internal
 */
final class UrlSlugType extends ObjectType
{
    use ServiceTrait;

    /** @var Data */
    protected $fieldDefinition;

    /**
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, ?Data $fieldDefinition = null, $config = [], $context = [])
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
                'resolve' => [$resolver, 'resolveSlug'],
            ],
            'siteId' => [
                'type' => Type::int(),
                'resolve' => [$resolver, 'resolveSiteId'],
            ],
        ];

        return $fields;
    }
}
