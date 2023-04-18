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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentResolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Editable;
use Pimcore\Model\Document\Editable\Areablock;
use Pimcore\Model\Document\Editable\BlockInterface;

class PageSnippet
{
    use ServiceTrait;

    /**
     * @param array $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array|null
     *
     * @throws \Exception
     */
    public function resolveElements($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $documentId = $value['id'];
        $getInheritedValuesInput = $args['getInheritedValues'] ?? false;
        $document = Document::getById($documentId, ['force' => $getInheritedValuesInput]);

        if ($document instanceof Document\PageSnippet) {
            $result = [];
            $sortBy = [];

            $getInheritedValues = Document\PageSnippet::getGetInheritedValues();
            Document\PageSnippet::setGetInheritedValues($getInheritedValuesInput);

            $elements = $document->getEditables();

            Document\PageSnippet::setGetInheritedValues($getInheritedValues);

            $service = $this->getGraphQlService();
            $supportedTypeNames = $service->getSupportedDocumentElementQueryDataTypes();

            foreach ($elements as $name => $element) {
                $elementType = $element->getType();
                if (in_array($elementType, $supportedTypeNames)) {
                    $result[] = $element;
                    $sortBy[$name] = $this->getElementSortIndex($name, $elements);
                }
            }

            usort($result, function (Editable $a, Editable $b) use ($sortBy) {
                // "Natural order" comparison so that "10" is ordered after "2"
                return strnatcmp($sortBy[$a->getName()], $sortBy[$b->getName()]);
            });

            return $result;
        }

        return null;
    }

    /**
     * Return a string to sort the elements by to get them in the same order as they are in the blocks.
     *
     * @param string $elementName
     * @param Editable[] $elements
     *
     * @return string
     */
    private function getElementSortIndex($elementName, $elements)
    {
        // "areablock:1.block:2.input" => ["areablock", "1", "block", "2", "input"]
        $parts = preg_split('/:(\d+)\./', $elementName, -1, PREG_SPLIT_DELIM_CAPTURE);

        $sortIndices = [];
        $blockName = '';

        for ($i = 1, $count = count($parts); $i < $count; $i += 2) {
            $blockName .= $parts[$i - 1]; // "areablock"
            $blockKey = $parts[$i]; // "1"

            $block = $elements[$blockName];
            if ($block instanceof BlockInterface) {
                $indices = $block->getData();
                if ($block instanceof Areablock) {
                    $indices = array_column($indices, 'key');
                }

                $index = array_search($blockKey, $indices);
                if ($index !== false) {
                    $sortIndices[] = $index;
                }
            }

            $blockName .= ":$blockKey."; // "areablock:1."
        }

        return implode(' ', $sortIndices);
    }
}
