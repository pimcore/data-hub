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

namespace Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model;

use Pimcore\Event\Traits\RequestAwareTrait;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class OutputCachePreLoadEvent extends Event
{
    use RequestAwareTrait;

    /**
     * @var Request
     */
    protected $request;

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

    /**
     * @param bool $useCache
     */
    public function setUseCache(bool $useCache)
    {
        $this->useCache = $useCache;
    }

    /**
     * @param Request $request
     * @param bool $useCache
     */
    public function __construct(Request $request, bool $useCache)
    {
        $this->request = $request;
        $this->useCache = $useCache;
    }
}
