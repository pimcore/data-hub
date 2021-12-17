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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementTag;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class DocumentLinkInputType extends InputObjectType
{
    use ServiceTrait;

    /**
     * @param Service $graphQlService
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'document_link_input'], $context = [])
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
            'internal' => Type::int(),
            'internalType' => Type::string(),
//            'object' => new InputObjectType([
//                "name" => "document_link_input_object",
//                "fields" =>
//                    [
//                        'type' => Type::string(),
//                        'id' => Type::int()
//                    ]]),
            'direct' => Type::string(),
            'linktype' => Type::string(),
            'href' => Type::string(),
            'tags' => ElementTag::getElementTagInputTypeDefinition(),
        ];
    }
}
