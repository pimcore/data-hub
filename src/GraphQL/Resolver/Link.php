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
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class Link
{
    use ServiceTrait;

    /**
     * @param mixed $value
     * @param array $args
     * @param array $context
     *
     * @return string|null
     *
     * @throws \Exception
     */
    public function resolveText($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveLinkValue($value, 'text');
    }

    /**
     * @param mixed $value
     * @param array $args
     * @param array $context
     *
     * @return string|null
     *
     * @throws \Exception
     */
    public function resolvePath($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveLinkValue($value, 'path');
    }

    /**
     *
     * @return null
     */
    public function resolveTarget($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveLinkValue($value, 'target');
    }

    /**
     *
     * @return null
     */
    public function resolveAnchor($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveLinkValue($value, 'anchor');
    }

    /**
     *
     * @return null
     */
    public function resolveTitle($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveLinkValue($value, 'title');
    }

    /**
     *
     * @return null
     */
    public function resolveAccesskey($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveLinkValue($value, 'accesskey');
    }

    /**
     *
     * @return null
     */
    public function resolveRel($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveLinkValue($value, 'rel');
    }

    /**
     *
     * @return null
     */
    public function resolveClass($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveLinkValue($value, 'class');
    }

    /**
     *
     * @return null
     */
    public function resolveAttributes($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveLinkValue($value, 'attributes');
    }

    /**
     *
     * @return null
     */
    public function resolveTabindex($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveLinkValue($value, 'tabindex');
    }

    /**
     *
     * @return null
     */
    public function resolveParameters($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        return $this->resolveLinkValue($value, 'parameters');
    }

    /**
     *
     * @return null
     */
    protected function resolveLinkValue(?\Pimcore\Model\DataObject\Data\Link $value, string $property)
    {
        if ($value instanceof \Pimcore\Model\DataObject\Data\Link) {
            $getter = 'get' . ucfirst($property);

            return $value->{$getter}();
        }

        return null;
    }
}
