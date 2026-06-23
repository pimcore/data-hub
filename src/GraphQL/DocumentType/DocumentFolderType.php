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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\General\FolderType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

/**
 * @internal
 */
final class DocumentFolderType extends FolderType
{
    /**
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $config = [], $context = [])
    {
        parent::__construct($graphQlService, ['name' => 'document_folder'], $context);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $propertyType = $this->getGraphQlService()->buildGeneralType('element_property');
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Element('document', $this->getGraphQLService());
        $documentResolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Document(new \Pimcore\Model\Document\Service(), $this->getGraphQlService());
        $documentTree = $this->getGraphQlService()->buildGeneralType('document_tree');
        $documentTranslation = $this->getGraphQlService()->buildGeneralType('document_translation');

        {
            $config['fields'] = [
                'id' => [
                    'name' => 'id',
                    'type' => Type::id(),
                ],
                'filename' => Type::string(),
                'fullpath' => [
                    'type' => Type::string(),
                ],
                'creationDate' => [
                    'type' => Type::string(),
                    'resolve' => [$resolver, 'resolveCreationDate'],
                ],
                'modificationDate' => [
                    'type' => Type::string(),
                    'resolve' => [$resolver, 'resolveModificationDate'],
                ],
                'type' => Type::string(),
                'properties' => [
                    'type' => Type::listOf($propertyType),
                    'args' => [
                        'keys' => [
                            'type' => Type::listOf(Type::string()),
                            'description' => 'List of property key names to include (if omitted, all properties are returned).',
                        ],
                    ],
                    'resolve' => [$resolver, 'resolveProperties'],
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
                ],
            ];
        }
    }
}
