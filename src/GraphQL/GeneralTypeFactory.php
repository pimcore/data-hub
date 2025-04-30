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

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

/**
 * @internal
 */
final class GeneralTypeFactory
{
    use ServiceTrait;

    public static $registry = [];

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
     * @return mixed
     */
    public function build()
    {
        if (!isset(self::$registry[$this->className])) {
            $operatorImpl = new $this->className($this->getGraphQlService());
            self::$registry[$this->className] = $operatorImpl;
        }

        return self::$registry[$this->className];
    }
}
