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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\SharedType;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

/**
 * @internal
 */
final class KeyValueType extends InputObjectType
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
                    'value' => Type::string(),
                ],
            ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }

    /**
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
