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

use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class PageType extends PageSnippetType
{
    /**
     * @param Service $graphQlService
     * @param DocumentElementType $documentElementType
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, DocumentElementType $documentElementType, $config = ['name' => 'document_page'], $context = [])
    {
        parent::__construct($graphQlService, $documentElementType, $config, $context);
    }
}
