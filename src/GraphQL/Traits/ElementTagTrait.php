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
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */
declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Traits;

use Pimcore\Model\Element\Tag;

trait ElementTagTrait
{

    /**
     * @param string $element_type
     * @param int $id
     * @return array
     */
    protected function getTags(string $element_type,int $id) {
        $tag = new Tag();
        $tags = $tag->getDao()->getTagsForElement($element_type, $id);
        $result = [];
        if ($tags) {
            foreach($tags as $tag) {
                $result[] = [
                    'id' => $tag->getId(),
                    'name' => $tag->getName(),
                    'path' => $tag->getNamePath(),
                ];
            }

        }
        return $result;
    }

}