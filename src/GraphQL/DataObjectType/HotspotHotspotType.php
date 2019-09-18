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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class HotspotHotspotType
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType
 */
class HotspotHotspotType extends ObjectType
{
    /**
     * @var self
     */
    protected static $instance;

    /**
     * @return HotspotHotspotType
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            $metadataConfig['fields']['type'] = Type::string();
            $config = [
                'fields' => [
                    'top' => Type::float(),
                    'left' => Type::float(),
                    'height' => Type::float(),
                    'width' => Type::float(),
                    'data' => Type::listOf(new ElementMetadataKeyValuePairType($metadataConfig)),
                    'name' => Type::string(),
                ],
            ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
