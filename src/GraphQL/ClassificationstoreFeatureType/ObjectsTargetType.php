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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\CoreBundle\DependencyInjection\ContainerAwareInterface;
use Pimcore\Bundle\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

/**
 * The object classes of the endpoint, resolved when the schema is built so that the members follow the configuration.
 *
 * @internal
 */
final class ObjectsTargetType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ServiceTrait;

    protected static $instance;

    public function __construct(Service $graphQlService, $config = ['name' => 'csFeatureObjectTarget'])
    {
        $this->setGraphQLService($graphQlService);

        parent::__construct($config);
    }

    public static function getInstance(Service $graphQlService): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new static($graphQlService);
        }

        return self::$instance;
    }

    public function getTypes(): array
    {
        if (!$this->getGraphQlService()->querySchemaEnabled('object')) {
            return [];
        }

        return array_values(ClassTypeDefinitions::getAll(true));
    }

    public function resolveType($element, $context, ResolveInfo $info): ObjectType|string|callable|Deferred|null
    {
        if ($element && $element['__elementType'] === 'object') {
            return ClassTypeDefinitions::get($element['__elementSubtype']);
        }

        return null;
    }
}
