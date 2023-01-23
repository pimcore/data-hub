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

use Exception;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * @deprecated will be removed in Data Hub 2
 */
class MergeType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    use ServiceTrait;

    /** @var array */
    protected $nodeDef;

    /** @var ClassDefinition|null */
    protected $class;

    /** @var object|null */
    protected $container;

    /**
     * @param Service $graphQlService
     * @param array $nodeDef
     * @param ClassDefinition|null $class
     * @param object|null $container
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
     * @return ClassDefinition|null
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param ClassDefinition|null $class
     */
    public function setClass($class): void
    {
        $this->class = $class;
    }

    public function getTypes(): array
    {
        $nodeDef = $this->nodeDef;
        $childTypes = [];
        $attributes = $nodeDef['attributes'];
        $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();

        if ($attributes['children']) {
            foreach ($attributes['children'] as $childDef) {
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
     *
     * @throws Exception
     */
    public function resolveType($element, $context, ResolveInfo $info)
    {
        if ($element) {
            if ($element instanceof DataObject) {
                $concrete = ($element instanceof DataObject\Concrete) ? $element : DataObject\Concrete::getById($element->getId());

                return ClassTypeDefinitions::get($concrete->getClassName());
            }
            if ($element instanceof Asset) {
                return $this->getGraphQlService()->buildAssetType('asset');
            }
        }

        return null;
    }

    /**
     * @param array $childTypes
     * @param array $result
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
