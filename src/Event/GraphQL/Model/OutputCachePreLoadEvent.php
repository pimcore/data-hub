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

namespace Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model;

use Pimcore\Event\Traits\RequestAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

final class OutputCachePreLoadEvent extends Event
{
    use RequestAwareTrait;

    /**
     * @var bool
     */
    protected $useCache;

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return bool
     */
    public function isUseCache()
    {
        return $this->useCache;
    }

    public function setUseCache(bool $useCache)
    {
        $this->useCache = $useCache;
    }

    public function __construct(Request $request, bool $useCache)
    {
        $this->request = $request;
        $this->useCache = $useCache;
    }
}
