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
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\PropertyType;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ObjectsType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    use ServiceTrait;

    /**
     * ObjectsType constructor.
     * @param Service $graphQlService
     */
    public function __construct(Service $graphQlService)
    {
        $this->setGraphQLService($graphQlService);
        parent::__construct(["name" => "hotspot_metadata_object"]);
    }
    /**
     * @return array|\GraphQL\Type\Definition\ObjectType[]
     *
     * @throws \Exception
     */
    public function getTypes()
    {
        $types = array_values(ClassTypeDefinitions::getAll(true));
        return $types;
    }

    /**
     * @inheritdoc
     */
    public function resolveType($element, $context, ResolveInfo $info)
    {
        if ($element) {
            if ($element['__elementType'] == 'object') {
                $type = ClassTypeDefinitions::get($element['__elementSubtype']);

                return $type;
            }
        }

        return null;
    }

}
