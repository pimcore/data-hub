<?php

declare(strict_types=1);

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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator\Factory;

use Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator\OperatorInterface;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

abstract class DefaultOperatorFactoryBase implements OperatorFactoryInterface
{
    use ServiceTrait;

    /**
     * @var string
     */
    protected $className;

    /**
     * @param string $className
     */
    public function __construct(Service $graphQlService, string $className)
    {
        $this->className = $className;
        $this->setGraphQLService($graphQlService);
    }

    /**
     * @param array $configElement
     * @param mixed $context
     *
     * @return OperatorInterface
     */
    public function build(array $configElement = [], $context = null): OperatorInterface
    {
        $operatorImpl = new $this->className($configElement, $context);
        $operatorImpl->setGraphQlService($this->getGraphQlService());

        return $operatorImpl;
    }
}
