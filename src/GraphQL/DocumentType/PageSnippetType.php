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

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class PageSnippetType extends AbstractDocumentType
{

    protected $documentElementType;

    /**
     * PageSnippetType constructor.
     * @param Service $graphQlService
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, DocumentElementType $documentElementType, $config = ["name" => "document_pageSnippet"], $context = [])
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
            'resolve' => [$resolver, "resolveElements"]
        ];
    }
}
