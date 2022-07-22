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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\ListingEvents;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\ListingEvent;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TranslationListing
{
    use ServiceTrait;

    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(Service $graphQlService, EventDispatcherInterface $eventDispatcher)
    {
        $this->setGraphQLService($graphQlService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function resolveEdge(mixed $value = null, array $args = [], array $context = [], ResolveInfo $resolveInfo = null): mixed
    {
        $translation = $value['node'];
        $data = new ElementDescriptor();

        $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();

        return $fieldHelper->extractData($data, $translation, $args, $context, $resolveInfo);
    }

    public function resolveEdges(mixed $value = null, array $args = [], array $context = [], ResolveInfo $resolveInfo = null): mixed
    {
        return $value['edges'];
    }

    /**
     * @throws \Exception
     */
    public function resolveListing(mixed $value = null, array $args = [], array $context = [], ResolveInfo $resolveInfo = null): array
    {
        // get list of types
        $list = new \Pimcore\Model\Translation\Listing();

        if (!empty($args['keys'])) {
            $keysArray = explode(',', $args['keys']);
            $keysString = "'" . implode("','", $keysArray) . "'" ;
            $list->setCondition('translations_messages.key IN (' . $keysString . ')');
        }

        if (!empty($args['languages'])) {
            $languages = str_replace(' ', '', $args['languages']);
            $list->setLanguages(explode(',', $languages));
        }

        if (!empty($args['domain'])) {
            $list->setDomain($args['domain']);
        }

        // sorting
        if (!empty($args['sortBy'])) {
            $list->setOrderKey($args['sortBy']);
            if (!empty($args['sortOrder'])) {
                $list->setOrder($args['sortOrder']);
            }
        }

        // paging
        if (isset($args['first'])) {
            $list->setLimit($args['first']);
        }

        if (isset($args['after'])) {
            $list->setOffset($args['after']);
        }

        $event = new ListingEvent(
            $list,
            $args,
            $context,
            $resolveInfo
        );
        $this->eventDispatcher->dispatch($event, ListingEvents::PRE_LOAD);
        $list = $event->getListing();

        $totalCount = $list->count();
        $list->getData();

        $nodes = [];

        foreach ($list as $translation) {
            $nodes[] = [
                'cursor' => 'translation-' . $translation->getKey(),
                'node' => $translation,
            ];
        }
        $connection = [];
        $connection['edges'] = $nodes;
        $connection['totalCount'] = $totalCount;

        return $connection;
    }

    public function resolveListingTotalCount(mixed $value = null, array $args = [], array $context = [], ResolveInfo $resolveInfo = null): mixed
    {
        return $value['totalCount'];
    }
}
