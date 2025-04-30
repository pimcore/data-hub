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

use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

/**
 * @internal
 */
final class SnippetType extends PageSnippetType
{
    /**
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, DocumentElementType $documentElementType, $config = ['name' => 'document_snippet'], $context = [])
    {
        parent::__construct($graphQlService, $documentElementType, $config, $context);
    }
}
