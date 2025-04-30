<?php

declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
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

    public function __construct(Service $graphQlService, string $className)
    {
        $this->className = $className;
        $this->setGraphQLService($graphQlService);
    }

    /**
     * @param array|null $context
     *
     */
    public function build(array $configElement = [], $context = null): OperatorInterface
    {
        $operatorImpl = new $this->className($configElement, $context);
        $operatorImpl->setGraphQlService($this->getGraphQlService());

        return $operatorImpl;
    }
}
