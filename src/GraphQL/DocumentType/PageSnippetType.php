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

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\SharedType\KeyValueType;
use Pimcore\Model\Document\PageSnippet;

class PageSnippetType extends AbstractDocumentType
{
    protected $documentElementType;

    /**
     * @param Service $graphQlService
     * @param DocumentElementType $documentElementType
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, DocumentElementType $documentElementType, $config = ['name' => 'document_pageSnippet'], $context = [])
    {
        $this->documentElementType = $documentElementType;
        parent::__construct($graphQlService, $config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentResolver\PageSnippet();
        $resolver->setGraphQLService($this->getGraphQlService());

        $this->buildBaseFields($config);
        $config['fields']['elements'] = [
            'type' => Type::listOf($this->documentElementType),
            'resolve' => [$resolver, 'resolveElements']
        ];

        $config['fields']['editables'] = [
            'type' => Type::listOf($this->documentElementType),
            'args' => [
                'getInheritedValues' => [
                    'type' => Type::boolean(),
                    'description' => 'Whether inherited editables should be fetched or not.',
                    'defaultValue' => false
                ],
            ],
            'resolve' => [$resolver, 'resolveElements']
        ];

        $config['fields']['title'] = [
            'type' => Type::string()
        ];

        $config['fields']['description'] = [
            'type' => Type::string()
        ];

        $keyValue = new ListOfType(KeyValueType::getInstance());

        $config['fields']['rendered'] = [
            'type' => Type::string(),
            'args' => [
                'attributes' => [
                    'type' => $keyValue,
                    'description' => 'Attributes passed into the controller/action',
                    'defaultValue' => []
                ],
                'query' => [
                    'type' => $keyValue,
                    'description' => 'Query Params passed into the controller/action',
                    'defaultValue' => []
                ],
                'options' => [
                    'type' => $keyValue,
                    'description' => 'Options passed into the controller/action',
                    'defaultValue' => []
                ],
                'use_layout' => [
                    'type' => Type::boolean(),
                    'description' => 'Disable Layout Rendering'
                ]
            ],
            'resolve' => static function ($value, $args) {
                $documentId = $value['id'];
                $document = PageSnippet::getById($documentId);

                $attributes = KeyValueType::resolveAssociativeArray($args['attributes']);
                $query = KeyValueType::resolveAssociativeArray($args['query']);
                $options = KeyValueType::resolveAssociativeArray($args['options']);

                if ($document instanceof PageSnippet) {
                    return \Pimcore\Model\Document\Service::render(
                        $document,
                        $attributes,
                        $args['use_layout'] ?? false,
                        $query,
                        $options
                    );
                }

                return null;
            }
        ];
    }
}
