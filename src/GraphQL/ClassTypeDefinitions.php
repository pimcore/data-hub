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

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\PimcoreObjectType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\DataObject\ClassDefinition;

/**
 * @internal
 */
final class ClassTypeDefinitions
{
    /**
     * @var array
     */
    public static $definitions = [];

    /**
     * Class definitions of the current schema build, by class name. Loaded with one query in build()
     * so that the type factory and the query/mutation builders do not look up every class by name again.
     *
     * @var array<string, ClassDefinition>
     */
    private static array $classDefinitions = [];

    /**
     * @param array $context
     */
    public static function build(Service $graphQlService, $context = [])
    {
        self::$classDefinitions = [];
        // one query for all classes; also fills the runtime cache entries (class_<id>)
        // that PimcoreObjectType::build() reads through ClassDefinition::getById()
        foreach ((new ClassDefinition\Listing())->load() as $classDefinition) {
            self::$classDefinitions[$classDefinition->getName()] = $classDefinition;
        }

        foreach (self::$classDefinitions as $name => $classDefinition) {
            self::$definitions[$name] = $graphQlService->buildDataObjectType($name, [], $context);
        }

        /**
         * @var PimcoreObjectType $definition
         */
        foreach (self::$definitions as $definition) {
            $definition->build($context);
        }
    }

    /**
     * Returns the class definition loaded by build(); falls back to a lookup by name for classes unknown to it
     * or renamed since (the map keeps the name the definition had when it was loaded).
     *
     * @throws \Exception
     */
    public static function getClassDefinition(string $className): ?ClassDefinition
    {
        $loaded = self::$classDefinitions[$className] ?? null;
        if ($loaded !== null && $loaded->getName() === $className) {
            return $loaded;
        }

        return ClassDefinition::getByName($className);
    }

    /**
     * @param string|ClassDefinition $class
     *
     * @return PimcoreObjectType
     *
     * @throws \Exception
     */
    public static function get($class)
    {
        $className = is_string($class) ? $class : $class->getName();
        $result = self::$definitions[$className] ?? null;
        if (!$result) {
            throw new ClientSafeException('type definition ' . $className . ' not found');
        }

        return $result;
    }

    /**
     * @param bool $onlyQueryTypes
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function getAll($onlyQueryTypes = false)
    {
        if ($onlyQueryTypes) {
            $context = RuntimeCache::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
            /** @var Configuration $configuration */
            $configuration = $context['configuration'];
            $types = array_keys($configuration->getConfiguration()['schema']['queryEntities']);
            $result = [];
            foreach ($types as $type) {
                if (isset(self::$definitions[$type])) {
                    $result[] = self::$definitions[$type];
                }
            }

            return $result;
        }

        return self::$definitions;
    }
}
