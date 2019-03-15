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
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use Pimcore\Bundle\DataHubBundle\GraphQL\Type\PimcoreObjectType;
use Pimcore\Model\DataObject\ClassDefinition;

class ClassTypeDefinitions
{
    /**
     * @var array
     */
    public static $definitions = [];

    /**
     * @param array $context
     */
    public static function build($context = [])
    {
        $listing = new ClassDefinition\Listing();
        $listing = $listing->load();

//        PimcoreObjectType::setSkipOperators(true);
//
//        foreach ($listing as $class) {
//            $objectType = new PimcoreObjectType($class, [], $context);;
//            self::$definitions[$class->getName()] = $objectType;
//            $objectType->build($context);
//        }
//
//        PimcoreObjectType::setSkipOperators(false);

        foreach ($listing as $class) {
            $objectType = new PimcoreObjectType($class, [], $context);
            self::$definitions[$class->getName()] = $objectType;
            $objectType->build($context);
        }
    }

    /**
     * @param $class
     *
     * @return AbstractObjectType
     *
     * @throws \Exception
     */
    public static function get($class)
    {
        $className = is_string($class) ? $class : $class->getName();
        $result = self::$definitions[$className];
        if (!$result) {
            throw new \Exception('type definition ' . $className . ' not found');
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function getAll()
    {
        return self::$definitions;
    }
}
