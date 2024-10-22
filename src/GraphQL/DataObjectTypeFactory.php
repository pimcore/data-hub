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

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\ClassDefinition;

class DataObjectTypeFactory
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
    public function build(string $className, $config = [], $context = [])
    {
        if (!isset(self::$registry[$className])) {
            $class = ClassDefinition::getByName($className);
            $operatorImpl = new $this->className($this->getGraphQlService(), $className, $class->getId(), $config, $context);
            self::$registry[$className] = $operatorImpl;
        }

        return self::$registry[$className];
    }
}
