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

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\PimcoreObjectType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Db;
use Pimcore\Model\DataObject\ClassDefinition;

class ClassTypeDefinitions
{
    /**
     * @var array
     */
    public static $definitions = [];

    /**
     * @param Service $graphQlService
     * @param array $context
     */
    public static function build(Service $graphQlService, $context = [])
    {
        $db = Db::get();
        $listing = $db->fetchAllAssociative('SELECT id, name FROM classes');

        foreach ($listing as $class) {
            $id = $class['id'];
            $name = $class['name'];
            $objectType = new PimcoreObjectType($graphQlService, $name, $id, [], $context);
            self::$definitions[$name] = $objectType;
        }

        /**
         * @var string $name
         * @var PimcoreObjectType $definition
         */
        foreach (self::$definitions as $name => $definition) {
            $definition->build($context);
        }
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
        $result = self::$definitions[$className];
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
            $context = RuntimeCache::get('datahub_context');
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
