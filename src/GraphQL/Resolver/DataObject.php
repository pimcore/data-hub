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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class DataObject extends Element
{
    use ServiceTrait;

    public function __construct(Service $graphQlService)
    {
        parent::__construct('object', $graphQlService);
    }

    /**
     * @param array            $value
     * @param array            $args
     * @param array            $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return int
     */
    public function resolveIndex($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        if (null === $value) {
            return null;
        }

        $object = \Pimcore\Model\DataObject::getById($value['id']);

        if (!$object instanceof self) {
            return null;
        }

        return $object->getIndex();
    }

    /**
     * @param array            $value
     * @param array            $args
     * @param array            $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return string
     */
    public function resolveChildrenSortBy($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        if (null === $value) {
            return null;
        }

        $object = \Pimcore\Model\DataObject::getById($value['id']);

        if (!$object instanceof self) {
            return null;
        }

        return $object->getChildrenSortBy();
    }
}
