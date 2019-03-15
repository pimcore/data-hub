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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldConfigGenerator;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Type\HrefType;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeDefinitionInterface;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\Service;

/**
 * Class Multihref
 *
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\FieldConfigGenerator
 */
class Objects extends Base implements TypeDefinitionInterface
{
    /**
     * @param Data $fieldDefinition
     * @param null $class
     * @param null $container
     *
     * @return mixed
     */
    public function getGraphQlFieldConfig(Data $fieldDefinition, $class = null, $container = null)
    {
        return $this->enrichConfig(
            [
                'name'    => $fieldDefinition->getName(),
                'type'    => $this->getFieldType($fieldDefinition, $class, $container),
                'resolve' => $this->getResolver($fieldDefinition, $class)
            ],
            $container
        );
    }

    /**
     * @param Data $fieldDefinition
     * @param null $class
     * @param null $container
     *
     * @return \GraphQL\Type\Definition\ListOfType|mixed
     */
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        return Type::listOf(new HrefType($fieldDefinition, $class));
    }

    /**
     * @param Data $fieldDefinition
     * @param $class
     *
     * @return \Closure
     */
    public function getResolver($fieldDefinition, $class)
    {
        return function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) use (
            $fieldDefinition,
            $class
        ) {
            $containerObjectId = $value['id'];
            $o = AbstractObject::getById($containerObjectId);
            if ($o) {
                $getter = 'get'.ucfirst($fieldDefinition->getName());
                $relations = $o->$getter();
                $result = [];
                if ($relations) {
                    /** @var $relation AbstractElement */
                    foreach ($relations as $relation) {
                        if (!WorkspaceHelper::isAllowed($relation, $context['configuration'], 'read')) {
                            if (PimcoreDataHubBundle::getNotAllowedPolicy() == PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION) {
                                throw new \Exception('not allowed to view ' . $relation->getFullPath());
                            } else {
                                continue;
                            }
                        }

                        $data = new \ArrayObject();
                        $data->setFlags(\ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);

                        $fieldHelper = \Pimcore::getContainer()->get('pimcore.datahub.graphql.fieldhelper.object');
                        $fieldHelper->extractData($data, $relation, $args, $context, $resolveInfo);

                        $type = Service::getType($relation);
                        if ($relation instanceof Concrete) {
                            $subtype = $relation->getClass()->getName();
                            $data['__elementType'] = $type;
                            $data['__elementSubtype'] = $subtype;
                        } elseif ($relation instanceof Asset) {
                            $data['data'] = $data['data'] ? base64_encode($data['data']) : null;
                            $data['__elementType'] = 'asset';
                            $data['__elementSubtype'] = $relation->getType();
                        } else {
                            continue;
                        }

                        $result[] = $data;
                    }

                    return $result;
                }
            }

            return null;
        };
    }
}
