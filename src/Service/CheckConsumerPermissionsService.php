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

use Pimcore\Bundle\DataHubBundle\Configuration;
use Symfony\Component\HttpFoundation\Request;

class CheckConsumerPermissionsService
{
    /**
     * @param Request $request
     * @param Configuration $configuration
     *
     * @return void
     */
    public function performSecurityCheck(Request $request, Configuration $configuration): bool
    {
        $securityConfig = $configuration->getSecurityConfig();
        if ($securityConfig['method'] === Configuration::SECURITYCONFIG_AUTH_APIKEY) {
            $apiKey = $request->headers->get('apikey');
            if(empty($apiKey)) {
                $apiKey = $request->headers->get('X-API-Key');
            }
            if(empty($apiKey)) {
                $apiKey = $request->get('apikey');
            }         
            return $apiKey === $securityConfig['apikey'];
        }
        return false;
    }
}
