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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ElementTagTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\AbstractObject;

/**
 * @internal
 */
final class DataObject extends Element
{
    use ServiceTrait, ElementTagTrait;

    public function __construct(Service $graphQlService)
    {
        parent::__construct('object', $graphQlService);
    }

    /**
     * @param array $value
     * @param array $args
     * @param array $context
     *
     * @return array|null
     *
     * @throws \Exception
     */
    public function resolveTag($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null)
    {
        $object = \Pimcore\Model\DataObject::getById($value['id']);

        if ($object) {
            $result = $this->getTags('object', $object->getId());
            if ($result) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @param array|null $value
     * @param array $args
     * @param array $context
     *
     * @return int|null
     */
    public function resolveIndex($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null)
    {
        if (null === $value) {
            return null;
        }

        $object = \Pimcore\Model\DataObject::getById($value['id']);

        if (!$object instanceof AbstractObject) {
            return null;
        }

        return $object->getIndex();
    }

    /**
     * @param array|null $value
     * @param array $args
     * @param array $context
     *
     * @return string|null
     */
    public function resolveChildrenSortBy($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null)
    {
        if (null === $value) {
            return null;
        }

        $object = \Pimcore\Model\DataObject::getById($value['id']);

        if (!$object instanceof \Pimcore\Model\DataObject) {
            return null;
        }

        return $object->getChildrenSortBy();
    }
}
