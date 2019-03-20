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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldConfigGenerator;

class Video extends AssetBase
{
    /** Return the actual asset (AbstractElement)
     * @param $asset
     *
     * @return mixed
     */
    public function getAssetElement($asset)
    {
        if ($asset instanceof \Pimcore\Model\DataObject\Data\Video) {
            return $asset->getData();
        }
    }
}
