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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class AbstractDocumentType extends ObjectType
{
    use ServiceTrait;

    /**
     * AbstractDocumentType constructor.
     * @param Service $graphQlService
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $config = [])
    {

        $this->setGraphQLService($graphQlService);
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function buildBaseFields(&$config)
    {

        $config['fields'] = [
            'creationDate' => Type::int(),
            'id' => ['name' => 'id',
                'type' => Type::id()
            ],
            'fullpath' => [
                'type' => Type::string()],
            'modificationDate' => Type::int(),
            'type' => Type::string(),
            'controller' => Type::string(),
            'action' => Type::string(),
            'template' => Type::string()
        ];
    }

}
