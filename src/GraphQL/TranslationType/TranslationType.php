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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\TranslationType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\SharedType\JsonType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class TranslationType extends ObjectType
{
    use ServiceTrait;

    protected string $fieldname;

    /**
     * @throws \Exception
     */
    public function __construct(Service $graphQlService, array $config = ['name' => 'translation', 'fields' => []])
    {
        $this->setGraphQLService($graphQlService);
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @throws \Exception
     */
    public function build(array &$config)
    {
        $config['fields'] = [
            'key' => Type::string(),
            'creationDate' => Type::int(),
            'modificationDate' => Type::int(),
            'domain' => Type::string(),
            'type' => Type::string(),
            'translations' => [
                'type' => new JsonType(),
            ]
        ];
    }
}
