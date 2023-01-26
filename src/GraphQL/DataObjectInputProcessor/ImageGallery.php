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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\Hotspotimage;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;

class ImageGallery extends Base
{
    /**
     * @param Concrete|AbstractData $object
     * @param mixed $newValue
     * @param array $args
     * @param array $context
     * @param ResolveInfo $info
     *
     * @throws \Exception
     */
    public function process($object, $newValue, $args, $context, ResolveInfo $info)
    {
        $attribute = $this->getAttribute();
        $getter = 'get' . ucfirst($attribute);
        $currentGallery = $object->$getter();

        if ($currentGallery instanceof \Pimcore\Model\DataObject\Data\ImageGallery) {
            $currentItems = $currentGallery->getItems() ?: [];
        } else {
            $currentItems = [];
        }

        Service::setValue($object, $attribute, function ($container, $setter) use ($newValue, $currentItems) {
            $hotspotImages = [];
            $newGallery = [];

            if ($newValue === null) {
                return $container->$setter($newGallery);
            }

            if (! ($newValue['replace'] ?? false)) {
                foreach ($currentItems as $currentItem) {
                    if ($currentItem instanceof Hotspotimage) {
                        $hotspotImages[] = $currentItem;
                    }
                }
            }

            if (is_array($newValue['images'])) {
                foreach ($newValue['images'] as $imageValue) {
                    $hotspotImage = new Hotspotimage($imageValue['id']);

                    if ($hotspotImage instanceof Hotspotimage) {
                        $hotspotImages[] = $hotspotImage;
                    }
                }

                $newGallery = new \Pimcore\Model\DataObject\Data\ImageGallery($hotspotImages);
            }

            return $container->$setter($newGallery);
        });
    }
}
