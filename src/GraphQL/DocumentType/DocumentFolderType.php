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

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\General\FolderType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class DocumentFolderType extends FolderType
{
    /**
     * @param Service $graphQlService
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
                    'type' => Type::string()
                ],
                'creationDate' => Type::int(),
                'modificationDate' => Type::int(),
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
}
