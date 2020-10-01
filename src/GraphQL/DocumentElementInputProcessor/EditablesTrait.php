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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor;

use Pimcore\Model\Document\PageSnippet;
use Pimcore\Model\Document\Tag;

trait EditablesTrait
{

    public function cleanEditables(PageSnippet $document, $tagName)
    {
        if (method_exists($document, 'setEditable')) {
            $editables = $document->getEditables();
        } else {
            // this one is deprecated and will be removed with pimcore 7
            $editables = $document->getElements();
        }

        /** @var Tag $editable */
        foreach ($editables as $editable) {
            $name = $editable->getName();
            if ($name === $tagName || strpos($name, $tagName . ".") === 0) {
                $document->removeEditable($name);
            }
        }
    }
}

