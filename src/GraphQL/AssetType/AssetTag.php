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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\AssetType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class AssetTag extends ObjectType
{
    use ServiceTrait;

    /**
     * AssetTag constructor.
     *
     * @param $class
     */
    public function __construct(Service $graphQlService, $config = [])
    {
        $this->graphQlService = $graphQlService;
        $config['name'] = 'asset_tag';
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $config['fields'] = [
            'id' => Type::id(),
            'name' => Type::string(),
            'path' => Type::string()
        ];
    }
}
