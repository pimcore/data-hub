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
     */
    public function resolveChildren($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $obj = $this->getObjectFromValue($value);
        if ($obj) {
            return $this->extractMultipleObjects($obj->getChildren(), $args, $context, $resolveInfo);
        }
        return [];
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     */
    public function resolveSiblings($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $obj = $this->getObjectFromValue($value);
        if ($obj) {
            return $this->extractMultipleObjects($obj->getSiblings(), $args, $context, $resolveInfo);
        }
        return [];
    }

    /**
     * @param $value
     * @return AbstractObject|null
     */
    protected function getObjectFromValue($value) {
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
     */
    protected function extractMultipleObjects($objects, $args, $context, $resolveInfo)
    {
        $result = [];
        if ($objects) {
            foreach ($objects as $object) {
                $result[] = $this->extractSingleObject($object, $args, $context, $resolveInfo);
            }
        }
        return $result;
    }

    /**
     * @param AbstractObject $object
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     */
    protected function extractSingleObject($object, $args, $context, $resolveInfo)
    {
        $data = new ElementDescriptor($object);
        $this->fieldHelper->extractData($data, $object, $args, $context, $resolveInfo);
        return $data;
    }
}
