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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AssetMetadataItem extends ObjectType
{
    /**
     * AssetMetadataItem constructor.
     *
     * @param $class
     */
    public function __construct($config = [])
    {
        $config['name'] = 'asset_metadata_item';
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $config['fields'] = [
            'name'  => Type::string(),
            'type' => Type::string(),
            'data' => Type::string(),
            'language' => Type::string()
        ];
    }
}
