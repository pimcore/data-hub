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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ListOfType;
use Pimcore\Tool;

class LocalizedType extends InputObjectType
{
    /**
     * @var array<string, LocalizedType>
     */
    protected static $instances;

    /**
     * @param mixed $determinedType
     *
     * @return mixed
     */
    public static function getInstance($determinedType)
    {
        try {
            $determinedTypeName = $determinedType->toString();

            if ($determinedType instanceof ListOfType) {
                $determinedTypeName = $determinedType->getWrappedType()->toString() . 'List';
            }
        } catch (\Throwable $throwable) {
            return $determinedType;
        }

        if (!isset(self::$instances[$determinedTypeName])) {
            $config = ['name' => 'Localized' . $determinedTypeName];

            foreach (Tool::getValidLanguages() as $language) {
                $config['fields'][$language] = [
                    'type' => $determinedType
                ];
            }

            self::$instances[$determinedTypeName] = new static($config);
        }

        return self::$instances[$determinedTypeName];
    }
}
