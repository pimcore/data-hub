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

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class MergeType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    use ServiceTrait;

    protected $nodeDef;

    protected $class;

    protected $container;

    /**
     * MergeType constructor.
     *
     * @param Service $graphQlService
     * @param $nodeDef
     * @param null $class
     * @param null $container
     * @param array $config
     */
    public function __construct(Service $graphQlService, $nodeDef, $class = null, $container = null, $config = [])
    {
        $this->setGraphQLService($graphQlService);
        $this->nodeDef = $nodeDef;
        $this->class = $class;
        $this->container = $container;
        parent::__construct($config);
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class): void
    {
        $this->class = $class;
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        $nodeDef = $this->nodeDef;
        $childTypes = [];
        $attributes = $nodeDef['attributes'];
        $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();

        if ($attributes['childs']) {
            foreach ($attributes['childs'] as $childDef) {
                $type = $fieldHelper->getGraphQlTypeFromNodeConf($childDef, $this->class, $this->container);
                $childTypes[] = $type;
            }
        }

        $result = [];
        $this->buildChildTypes($childTypes, $result);

        return $result;
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
            } else {
                if ($element['__elementType'] == 'asset') {
                    return $this->getGraphQlService()->buildAssetType('asset');
                }
            }
        }

        return null;
    }

    /**
     * @param $childTypes
     * @param $result
     */
    public function buildChildTypes($childTypes, &$result)
    {
        if (!$childTypes) {
            return;
        }
        // this will always return a list type

        foreach ($childTypes as $childType) {
            if ($childType instanceof ListOfType) {
                $wrappedType = $childType->getWrappedType();
                $this->buildChildTypes([$wrappedType], $result);
            } else {
                if ($childType instanceof UnionType) {
                    $allowedTypes = $childType->getTypes();
                    $this->buildChildTypes($allowedTypes, $result);
                } else {
                    $result[$childType->name] = $childType;
                }
            }
        }
    }
}
