<?php

declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Service\Studio;

use Pimcore\Bundle\DataHubBundle\Event\Studio\PreResponse\ThumbnailEvent;
use Pimcore\Bundle\DataHubBundle\Hydrator\ThumbnailHydratorInterface;
use Pimcore\Model\Asset\Image\Thumbnail\Config;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/** @internal */
final readonly class ThumbnailService implements ThumbnailServiceInterface
{
    public function __construct(
        private ThumbnailHydratorInterface $thumbnailHydrator,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function getThumbnails(): array
    {
        $list = new Config\Listing();
        $items = $list->load();

        $thumbnails = [];
        foreach ($items as $item) {
            $hydratedThumbnail = $this->thumbnailHydrator->hydrate($item);

            $this->eventDispatcher->dispatch(
                new ThumbnailEvent($hydratedThumbnail),
                ThumbnailEvent::EVENT_NAME
            );

            $thumbnails[] = $hydratedThumbnail;
        }

        return $thumbnails;
    }
}
