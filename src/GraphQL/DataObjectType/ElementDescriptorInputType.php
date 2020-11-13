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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class ElementDescriptorInputType extends InputObjectType
{
    use ServiceTrait;


    /**
     * ElementDescriptorInputType constructor.
     * @param Service $graphQlService
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $config = ["name" => "ElementDescriptorInput"], $context = [])
    {
        $this->setGraphQLService($graphQlService);
        $this->build($config);
        parent::__construct($config);
    }


    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $config['fields'] = [
            'type' => Type::string(),
            'id' => Type::int(),
            'fullpath' => Type::string()
        ];
        $config['description'] = "type can be omitted for mutations only allowing one type, e.g. many-to-many-objects";
    }
}
