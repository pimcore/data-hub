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

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class ElementTag extends ObjectType
{
    use ServiceTrait;

    protected static $tagTypeCache = [];

    /**
     * Type definition for ElementTag
     *
     * @return array
     */
    public static function getElementTagInputTypeDefinition()
    {
        if (!isset(self::$tagTypeCache['ElementTag'])) {
            self::$tagTypeCache['ElementTag'] = [
                'type' => Type::listOf(new InputObjectType([
                    'name' => 'ElementTag',
                    'fields' => [
                        'id' => Type::id(),
                        'path' => Type::string(),
                    ]
                ]))
            ];
        }

        return self::$tagTypeCache['ElementTag'];
    }

    /**
     * @param Service $graphQlService
     * @param array $config
     */
    public function __construct(Service $graphQlService, $config = [])
    {
        $this->graphQlService = $graphQlService;
        $config['name'] = 'element_tag';
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
