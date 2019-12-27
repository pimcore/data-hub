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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DataObjectFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;


class PimcoreObject
{
    use ServiceTrait;

    /** @var DataObjectFieldHelper */
    protected $fieldHelper;

    /**
     * PimcoreObject constructor.
     */
    public function __construct(DataObjectFieldHelper $fieldHelper)
    {
        $this->fieldHelper = $fieldHelper;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return ElementDescriptor
     * @throws \Exception
     */
    public function resolveParent($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $obj = $this->getObjectFromValue($value);
        if ($obj) {
            $parent = $obj->getParent();
            if ($parent) {
                return $this->extractSingleObject($parent, $args, $context, $resolveInfo);
            }
        }
        return null;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveChildren($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $obj = $this->getObjectFromValue($value);
        if ($obj) {
            $objectTypes = [AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_FOLDER];
            if (isset($args['objectTypes'])) {
                $objectTypes = $args['objectTypes'];
            }
            return $this->extractMultipleObjects($obj->getChildren($objectTypes), $args, $context, $resolveInfo);
        }
        return [];
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveSiblings($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $obj = $this->getObjectFromValue($value);
        if ($obj) {
            $objectTypes = [AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_FOLDER];
            if (isset($args['objectTypes'])) {
                $objectTypes = $args['objectTypes'];
            }
            return $this->extractMultipleObjects($obj->getSiblings($objectTypes), $args, $context, $resolveInfo);
        }
        return [];
    }

    /**
     * @param $value
     * @return AbstractObject|null
     * @throws \Exception
     */
    protected function getObjectFromValue($value)
    {
        if ($value instanceof ElementDescriptor) {
            $obj = AbstractObject::getById($value['id']);
            return $obj;
        }
        return null;
    }

    /**
     * @param array $objects
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    protected function extractMultipleObjects($objects, $args, $context, $resolveInfo)
    {
        $result = [];
        if ($objects) {
            foreach ($objects as $object) {
                $result[] = $this->extractSingleObject($object, $args, $context, $resolveInfo);
            }
        }
        return array_filter($result);
    }

    /**
     * @param AbstractObject $object
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    protected function extractSingleObject($object, $args, $context, $resolveInfo)
    {
        if (!WorkspaceHelper::isAllowed($object, $context['configuration'], 'read')) {
            if (PimcoreDataHubBundle::getNotAllowedPolicy() == PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION) {
                throw new \Exception('not allowed to view ' . $object->getFullPath());
            } else {
                return null;
            }
        }
        $data = new ElementDescriptor($object);
        $this->fieldHelper->extractData($data, $object, $args, $context, $resolveInfo);
        return $data;
    }
}
