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
use Pimcore\Model\DataObject\ClassDefinition;

final class DataObjectTypeFactory
{
    use ServiceTrait;

    public static $registry = [];

    protected string $className;

    public function __construct(Service $graphQlService, string $className)
    {
        $this->className = $className;
        $this->setGraphQLService($graphQlService);
    }

    /**
     * @return mixed
     */
    public function build(string $className, $config = [], $context = [])
    {
        if (!isset(self::$registry[$className])) {
            $class = ClassDefinition::getByName($className);
            $operatorImpl = new $this->className(
                $this->getGraphQlService(),
                $className,
                $class->getId(),
                $config,
                $context
            );
            self::$registry[$className] = $operatorImpl;
        }

        return self::$registry[$className];
    }
}
