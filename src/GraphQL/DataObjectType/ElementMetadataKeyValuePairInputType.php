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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class ElementMetadataKeyValuePairInputType
 *
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType
 *
 * @internal
 */
final class ElementMetadataKeyValuePairInputType extends InputObjectType
{
    /**
     * @var static|null
     */
    protected static $instance;

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        $config['name'] = 'element_metadata_item_key_value_pair_input';
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @return ElementMetadataKeyValuePairInputType
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $config['fields']['name'] = Type::string();
        $config['fields']['value'] = Type::string();
    }
}
