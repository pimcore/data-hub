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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor;

use Pimcore\Model\Document\PageSnippet;

trait EditablesTrait
{
    /**
     * @param string $editableName
     *
     * @return void
     */
    public function cleanEditables(PageSnippet $document, $editableName)
    {
        $editables = $document->getEditables();

        foreach ($editables as $editable) {
            $name = $editable->getName();
            if ($name === $editableName || strpos($name, $editableName . '.') === 0) {
                $document->removeEditable($name);
            }
        }
    }
}
