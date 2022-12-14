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

namespace Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model;

use GraphQL\Executor\ExecutionResult;
use Pimcore\Event\Traits\RequestAwareTrait;
use Pimcore\Event\Traits\ResponseAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class ExecutorResultEvent extends Event
{
    use RequestAwareTrait;
    use ResponseAwareTrait;

    /**
     * @var ExecutionResult
     */
    protected $result;

    /**
     * @return ExecutionResult
     */
    public function getResult()
    {
        return $this->result;
    }

    public function setResult(ExecutionResult $result)
    {
        $this->result = $result;
    }

    public function __construct(Request $request, ExecutionResult $result)
    {
        $this->request = $request;
        $this->result = $result;
    }
}
