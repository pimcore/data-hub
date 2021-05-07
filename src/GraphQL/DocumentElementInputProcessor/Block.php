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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Model\Document\PageSnippet;

class Block extends Base
{
    use EditablesTrait;

    /**
     * @param PageSnippet $document
     * @param mixed $newValue
     * @param array $args
     * @param mixed $context
     * @param ResolveInfo $info
     */
    public function process($document, $newValue, $args, $context, ResolveInfo $info)
    {
        $editableType = $newValue['_editableType'];

        $editable = $this->editableLoader->build($editableType);

        $editableName = $newValue['_editableName'];
        $editable->setName($editableName);

        $typeCache = &MutationType::$typeCache;

        $indices = [];
        if (is_array($newValue)) {
            if (array_key_exists('indices', $newValue)) {
                $indices = $newValue['indices'];
            }
        }

        $idx = 0;
        if (isset($newValue['items'])) {
            foreach ($newValue['items'] as $blockItem) {
                $editables = $blockItem['editables'] ?? [];

                $indices[$idx] = $idx + 1;

                if ($blockItem['replace'] ?? true) {
                    $this->cleanEditables($document, $editableName . ':' . ($idx + 1));
                }

                foreach ($editables as $editableType => $listByType) {
                    foreach ($listByType as $editableData) {
                        $editableData['_editableName'] = $editableName . ':' . ($idx + 1) . '.' . $editableData['_editableName'];
                        $editableData['_editableType'] = $editableType;
                        $typeDefinition = $typeCache[$editableType];
                        $processor = $typeDefinition['processor'];
                        call_user_func_array($processor, [$document, $editableData, $args, $context, $info]);
                    }
                }

                $idx++;
            }
        }

        ksort($indices);

        $editable->setDataFromEditmode($indices);

        if (method_exists($document, 'setElement')) {
            $document->setElement($editableName, $editable);
        } else {
            $document->setEditable($editable);
        }
    }
}
