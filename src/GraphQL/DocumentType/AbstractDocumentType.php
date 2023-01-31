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

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeInterface\Element;

abstract class AbstractDocumentType extends ObjectType
{
    use ServiceTrait;

    /**
     * @param Service $graphQlService
     * @param array $config
     */
    public function __construct(Service $graphQlService, $config = [])
    {
        $config['interfaces'] = [Element::getInstance()];
        $this->setGraphQLService($graphQlService);
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    abstract public function build(&$config);

    /**
     * @param array $config
     */
    public function buildBaseFields(&$config)
    {
        $propertyType = $this->getGraphQlService()->buildGeneralType('element_property');
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Element('document', $this->getGraphQLService());
        $documentResolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Document(new \Pimcore\Model\Document\Service(), $this->getGraphQlService());
        $documentTree = $this->getGraphQlService()->buildGeneralType('document_tree');
        $elementTagType = $this->getGraphQlService()->buildGeneralType('element_tag');
        $documentTranslation = $this->getGraphQlService()->buildGeneralType('document_translation');

        $config['fields'] = [
            'creationDate' => Type::int(),
            'id' => ['name' => 'id',
                'type' => Type::id()
            ],
            'fullpath' => [
                'type' => Type::string()],
            'modificationDate' => Type::int(),
            'published' => ['name' => 'published',
                'type' => Type::boolean()
            ],
            'type' => Type::string(),
            'controller' => Type::string(),
            'action' => Type::string(),
            'template' => Type::string(),
            'tags' => [
                'type' => Type::listOf($elementTagType),
                'args' => [
                    'name' => ['type' => Type::string()],
                ],
                'resolve' => [$resolver, 'resolveTag']
            ],
            'properties' => [
                'type' => Type::listOf($propertyType),
                'args' => [
                    'keys' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'comma seperated list of key names'
                    ]
                ],
                'resolve' => [$resolver, 'resolveProperties']
            ],
            'parent' => [
                'type' => $documentTree,
                'resolve' => [$resolver, 'resolveParent'],
            ],
            'children' => [
                'type' => Type::listOf($documentTree),
                'resolve' => [$resolver, 'resolveChildren'],
            ],
            '_siblings' => [
                'type' => Type::listOf($documentTree),
                'resolve' => [$resolver, 'resolveSiblings'],
            ],
            'translations' => [
                'args' => ['defaultLanguage' => ['type' => Type::string()]],
                'type' => Type::listOf($documentTranslation),
                'resolve' => [$documentResolver, 'resolveTranslations'],
            ]
        ];
    }
}
