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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\SharedType;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class KeyValueType extends InputObjectType
{
    /**
     * @var static|null
     */
    protected static $instance;

    /**
     * @return KeyValueType
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            $config = [
                'name' => 'KeyValue',
                'fields' => [
                    'key' => Type::string(),
                    'value' => Type::string()
                ],
            ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }

    /**
     * @param array|null $value
     *
     * @return array
     */
    public static function resolveAssociativeArray(?array $value)
    {
        if (null === $value) {
            return [];
        }

        $res = [];

        foreach ($value as $entry) {
            $res[$entry['key']] = $entry['value'];
        }

        return $res;
    }
}
