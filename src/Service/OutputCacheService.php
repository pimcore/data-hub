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

namespace Pimcore\Bundle\DataHubBundle\Service;

use Psr\Container\ContainerInterface;
use Pimcore\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $config = $container->getParameter('pimcore_data_hub');

        if (isset($config['graphql'])) {
            
            if (isset($config['graphql']['output_cache_enabled'])) {
                $this->cacheEnabled = filter_var($config['graphql']['output_cache_enabled'], FILTER_VALIDATE_BOOLEAN);
            }
            
            if (isset($config['graphql']['output_cache_lifetime'])) {
                $this->lifetime = intval($config['graphql']['output_cache_lifetime']);
            }
        }
    }
    
    
    public function load(Request $request) {
        
        if(!$this->useCache($request)) {
            return null;
        }
        
        $cacheKey = $this->computeKey($request);
        
        return \Pimcore\Cache::load($cacheKey);
    }
    
    
    public function save(Request $request, JsonResponse $response, $extraTags = []) : void {
        if ($this->useCache($request)) {
            $cacheKey = $this->computeKey($request);
            $clientname = $request->get('clientname');
            
            \Pimcore\Cache::save(
                $response,
                $cacheKey,
                array_merge(["output","datahub", $clientname], $extraTags),
                $this->lifetime);
        }
    }
    
    
    private function computeKey(Request $request) : string {
        $clientname = $request->get('clientname');
        
        $input = json_decode($request->getContent(), true);
        $input = print_r($input, true);
        
        return md5($clientname . $input);
    }
    
    private function useCache(Request $request) : bool {
        if(!$this->cacheEnabled) {
            Logger::debug("Output cache is disabled");
            return false;
        }
        
        $disableCacheForSingleRequest = false;
        
        if (\Pimcore::inDebugMode()){
            $disableCacheForSingleRequest = filter_var($request->query->get('pimcore_nocache', 'false'), FILTER_VALIDATE_BOOLEAN)
            || filter_var($request->query->get('pimcore_outputfilters_disabled', 'false'), FILTER_VALIDATE_BOOLEAN);
        }
        
        if($disableCacheForSingleRequest) {
            Logger::debug("Output cache is disabled for this request");
            return false;
        }
        
        
        return true;
    }
}
