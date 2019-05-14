<?php
declare(strict_types=1);
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class HotspotMarkerType
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\Type
 */
class HotspotMarkerType extends ObjectType
{
    /**
     * @var self
     */
    protected static $instance;

    /**
     * @return HotspotMarkerType
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            $metadataConfig['fields']['type'] = Type::string();
            $config = [
                'fields' => [
                    'top' => Type::float(),
                    'left' => Type::float(),
                    'data' => Type::listOf(new ElementMetadataKeyValuePairType($metadataConfig)),
                    'name' => Type::string(),
                ],
            ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
