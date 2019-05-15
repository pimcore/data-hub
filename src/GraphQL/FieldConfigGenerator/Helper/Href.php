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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldConfigGenerator\Helper;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\Service;

class Href
{

    use ServiceTrait;

    /**
     * @var
     */
    public $fieldDefinition;

    /**
     * @var
     */
    public $class;


    /**
     * Href constructor.
     * @param \Pimcore\Bundle\DataHubBundle\GraphQL\Service $graphQlService
     * @param $fieldDefinition
     * @param $class
     */
    public function __construct(\Pimcore\Bundle\DataHubBundle\GraphQL\Service $graphQlService, $fieldDefinition, $class)
    {
        $this->fieldDefinition = $fieldDefinition;
        $this->class = $class;
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
        $containerObjectId = $value['id'];
        $o = AbstractObject::getById($containerObjectId);
        if ($o) {
            $getter = 'get'.ucfirst($this->fieldDefinition->getName());
            /** @var $relation AbstractElement */
            $relation = $o->$getter();
            if ($relation) {
                if (!WorkspaceHelper::isAllowed($relation, $context['configuration'], 'read')) {
                    if (PimcoreDataHubBundle::getNotAllowedPolicy() == PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION) {
                        throw new \Exception('not allowed to view ' . $relation->getFullPath());
                    } else {
                        return null;
                    }
                }

                $data = new \ArrayObject();
                $data->setFlags(\ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);

                $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();
                $fieldHelper->extractData($data, $relation, $args, $context, $resolveInfo);

                $type = Service::getType($relation);
                if ($relation instanceof Concrete) {
                    $subtype = $relation->getClass()->getName();
                    $data['id'] = $relation->getId();
                    $data['__elementType'] = $type;
                    $data['__elementSubtype'] = $subtype;
                } elseif ($relation instanceof Asset) {
                    $data['data'] = $data['data'] ? base64_encode($data['data']) : null;
                    $data['id'] = $relation->getId();
                    $data['__elementType'] = 'asset';
                    $data['__elementSubtype'] = $relation->getType();
                }

                return $data;
            }
        }

        return null;
    }
}
