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

namespace Pimcore\Bundle\DataHubBundle\Service;

use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\OutputCachePreLoadEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\OutputCachePreSaveEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\OutputCacheEvents;
use Pimcore\Logger;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OutputCacheService
{
    /**
     * @var bool
     */
    private $cacheEnabled = false;

    /**
     * The cached items lifetime in seconds
     *
     * @var int
     */
    private $lifetime = 30;

    /**
     * @var EventDispatcherInterface
     */
    public $eventDispatcher;

    public function __construct(ContainerBagInterface $container, EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        $dataHubConfig = $container->get('pimcore_data_hub');
        if (isset($dataHubConfig['graphql'])) {
            if (isset($dataHubConfig['graphql']['output_cache_enabled'])) {
                $this->cacheEnabled = filter_var($dataHubConfig['graphql']['output_cache_enabled'], FILTER_VALIDATE_BOOLEAN);
            }

            if (isset($dataHubConfig['graphql']['output_cache_lifetime'])) {
                $this->lifetime = intval($dataHubConfig['graphql']['output_cache_lifetime']);
            }
        }
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function load(Request $request)
    {
        if (!$this->useCache($request)) {
            return null;
        }

        $cacheKey = $this->computeKey($request);

        return $this->loadFromCache($cacheKey);
    }

    /**
     * @param Request $request
     * @param JsonResponse $response
     * @param array $extraTags
     *
     * @return void
     */
    public function save(Request $request, JsonResponse $response, $extraTags = []): void
    {
        if ($this->useCache($request)) {
            $cacheKey = $this->computeKey($request);
            $clientname = $request->get('clientname');
            $extraTags = array_merge(['output', 'datahub', $clientname], $extraTags);

            $event = new OutputCachePreSaveEvent($request, $response);
            $this->eventDispatcher->dispatch($event, OutputCacheEvents::PRE_SAVE);

            $this->saveToCache($cacheKey, $event->getResponse(), $extraTags);
        }
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    protected function loadFromCache($key)
    {
        return \Pimcore\Cache::load($key);
    }

    /**
     * @param string $key
     * @param mixed $item
     * @param array $tags
     *
     * @return void
     */
    protected function saveToCache($key, $item, $tags = []): void
    {
        \Pimcore\Cache::save($item, $key, $tags, $this->lifetime);
    }

    private function computeKey(Request $request): string
    {
        $clientname = $request->get('clientname');

        $input = json_decode($request->getContent(), true);
        $input = print_r($input, true);

        return md5('output_' . $clientname . $input);
    }

    private function useCache(Request $request): bool
    {
        if (!$this->cacheEnabled) {
            Logger::debug('Output cache is disabled');

            return false;
        }

        if (\Pimcore::inDebugMode()) {
            $disableCacheForSingleRequest = filter_var($request->query->get('pimcore_nocache', 'false'), FILTER_VALIDATE_BOOLEAN)
            || filter_var($request->query->get('pimcore_outputfilters_disabled', 'false'), FILTER_VALIDATE_BOOLEAN);

            if ($disableCacheForSingleRequest) {
                Logger::debug('Output cache is disabled for this request');

                return false;
            }
        }

        // So far, cache will be used, unless the listener denies it
        $event = new OutputCachePreLoadEvent($request, true);
        $this->eventDispatcher->dispatch($event, OutputCacheEvents::PRE_LOAD);

        return $event->isUseCache();
    }
}
