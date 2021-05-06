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

use Pimcore\Bundle\DataHubBundle\GraphQL\Query\QueryType;
use Pimcore\Event\Traits\RequestAwareTrait;
use Pimcore\Event\Traits\ResponseAwareTrait;
use Symfony\Contracts\EventDispatcher\Event;

class QueryTypeEvent extends Event
{
    use RequestAwareTrait;
    use ResponseAwareTrait;

    /**
     * @var QueryType
     */
    protected $queryType;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $context;

    /**
     * @return QueryType
     */
    public function getQueryType()
    {
        return $this->queryType;
    }

    /**
     * @param QueryType $queryType
     */
    public function setQueryType(QueryType $queryType)
    {
        $this->queryType = $queryType;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    /**
     * MutationEvent constructor.
     *
     * @param QueryType $queryType
     * @param $config
     * @param array $context
     */
    public function __construct(QueryType $queryType, $config, $context)
    {
        $this->queryType = $queryType;
        $this->config = $config;
        $this->context = $context;
    }
}
