<?php

declare(strict_types=1);

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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Traits;

use Pimcore\Model\Element\Tag;

trait ElementTagTrait
{
    /**
     * @param string $element_type
     * @param int $id
     *
     * @return array
     */
    protected function getTags(string $element_type, int $id)
    {
        $tag = new Tag();
        $tags = $tag->getDao()->getTagsForElement($element_type, $id);
        $result = [];
        if ($tags) {
            foreach ($tags as $tag) {
                $result[] = [
                    'id' => $tag->getId(),
                    'name' => $tag->getName(),
                    'path' => $tag->getNamePath(),
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $element_type
     * @param int $id
     * @param array $tags
     *
     * @return bool
     */
    protected function setTags(string $element_type, int $id, $tags)
    {
        $tag = new Tag;
        $tag->getDao()->setTagsForElement($element_type, $id, $tags);

        return true;
    }

    /**
     * @param array $input
     *
     * @return array|bool
     */
    protected function getTagsFromInput(array $input)
    {
        $tags = [];
        foreach ($input as $tag_input) {
            if (isset($tag_input['id']) && $tag_input['id']) {
                $tag = Tag::getById((int)$tag_input['id']);
            } elseif (isset($tag_input['path']) && $tag_input['path']) {
                $tag = Tag::getByPath($tag_input['path']);
            } else {
                return false;
            }
            if (!$tag) {
                return false;
            }
            $tags[] = $tag;
        }

        return $tags;
    }
}
