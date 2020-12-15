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

use GraphQL\Type\Schema;
use Pimcore\Event\Traits\RequestAwareTrait;
use Pimcore\Event\Traits\ResponseAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class ExecutorEvent extends Event
{
    use RequestAwareTrait;
    use ResponseAwareTrait;

    /**
     * @var mixed
     */
    protected $request;

    /**
     * @var string
     */
    protected $query;

    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @var array
     */
    protected $context;

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     * @param bool $asString
     */
    public function setRequest($request, $asString = true)
    {
        $this->request = $asString ? (string)$request : $request;
    }

    /**
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param Schema $schema
     */
    public function setSchema(Schema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context)
    {
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @param Request $request
     * @param array $query
     * @param Schema $schema
     * @param array $context
     */
    public function __construct(Request $request, $query, Schema $schema, $context)
    {
        $this->request = $request;
        $this->query = $query;
        $this->schema = $schema;
        $this->context = $context;
    }
}
