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

use GraphQL\Executor\ExecutionResult;
use Pimcore\Event\Traits\RequestAwareTrait;
use Pimcore\Event\Traits\ResponseAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

final class ExecutorResultEvent extends Event
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
