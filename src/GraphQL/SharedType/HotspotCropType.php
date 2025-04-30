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

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class HotspotCropType
 *
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\SharedType
 *
 * @internal
 */
final class HotspotCropType extends ObjectType
{
    /**
     * @var static|null
     */
    protected static $instance;

    /**
     * @return HotspotCropType
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            $config = [
                'fields' => [
                    'cropTop' => Type::float(),
                    'cropLeft' => Type::float(),
                    'cropHeight' => Type::float(),
                    'cropWidth' => Type::float(),
                    'cropPercent' => Type::boolean(),
                ],
            ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
