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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class LinkInputType extends InputObjectType
{
    use ServiceTrait;

    /**
     * @param Service $graphQlService
     * @param array $config
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'LinkInput'])
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
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\DataObject($this->getGraphQlService());
        $resolver->setGraphQLService($this->getGraphQlService());

        $config['fields'] = [
            'text' => Type::string(),
            'path' => Type::string(),
            'target' => new EnumType([
                'name' => 'target',
                'description' => 'Valid Link targets: "empty", "_blank", "_self", "_top", "_parent"',
                'values' => [
                    'empty' => ['value' => null],
                    '_blank',
                    '_self',
                    '_top',
                    '_parent'
                ]
            ]),
            'anchor' => Type::string(),
            'title' => Type::string(),
            'accesskey' => Type::string(),
            'rel' => Type::string(),
            'class' => Type::string(),
            'attributes' => Type::string(),
            'tabindex' => Type::string(),
            'parameters' => Type::string(),
        ];
    }
}
