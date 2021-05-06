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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Helper;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\AbstractElement;

class ReverseManyToManyObjects
{
    use ServiceTrait;

    /**
     * @var Data\ReverseManyToManyObjectRelation
     */
    public $fieldDefinition;

    /**
     * @var
     */
    public $class;

    /**
     * @var
     */
    public $attribute;

    /**
     * Objects constructor.
     *
     * @param \Pimcore\Bundle\DataHubBundle\GraphQL\Service $graphQlService
     * @param string $attribute
     * @param Data $fieldDefinition
     * @param $class
     */
    public function __construct(\Pimcore\Bundle\DataHubBundle\GraphQL\Service $graphQlService, $attribute, $fieldDefinition, $class)
    {
        $this->fieldDefinition = $fieldDefinition;
        $this->class = $class;
        $this->attribute = $attribute;
        $this->setGraphQLService($graphQlService);
    }

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array|null
     *
     * @throws \Exception
     */
    public function resolve($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $objectId = $value['id'];
        $object = Concrete::getById($objectId);

        $relations = $object->getRelationData($this->fieldDefinition->getOwnerFieldName(), false, $this->fieldDefinition->getOwnerClassId());
        if ($relations) {
            $result = [];
            /** @var $relation AbstractElement */
            foreach ($relations as $relationRaw) {
                $relation = Concrete::getById($relationRaw['id']);
                if ($relation) {
                    if (!WorkspaceHelper::checkPermission($relation, 'read')) {
                        continue;
                    }

                    $data = new ElementDescriptor($relation);
                    $this->getGraphQlService()->extractData($data, $relation, $args, $context, $resolveInfo);
                    $result[] = $data;
                }
            }

            return $result;
        }

        return $relations;
    }
}
