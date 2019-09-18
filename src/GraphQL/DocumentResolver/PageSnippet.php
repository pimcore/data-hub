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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentResolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Document;


class PageSnippet
{

    use ServiceTrait;


    /**
     * DocumentType constructor.
     */
    public function __construct()
    {
    }


    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveElements($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $documentId = $value['id'];
        $document = Document::getById($documentId);

        if ($document instanceof Document\PageSnippet) {
            $result = [];
            $elements = $document->getElements();

            $service = $this->getGraphQlService();
            $supportedTypeNames = $service->getSupportedDocumentElementQueryDataTypes();

            foreach ($elements as $element) {
                $elementType = $element->getType();
                if (in_array($elementType, $supportedTypeNames)) {
                    $result[] = $element;
                }

            }
            return $result;
        }

        return null;

    }

}

