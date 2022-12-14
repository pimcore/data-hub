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

use Pimcore\Bundle\DataHubBundle\Configuration;
use Symfony\Component\HttpFoundation\Request;

class CheckConsumerPermissionsService
{
    public const TOKEN_HEADER = 'X-API-Key';

    public function performSecurityCheck(Request $request, Configuration $configuration): bool
    {
        $securityConfig = $configuration->getSecurityConfig();
        if ($securityConfig['method'] === Configuration::SECURITYCONFIG_AUTH_APIKEY) {
            $apiKey = $request->headers->get('apikey');
            if (empty($apiKey)) {
                $apiKey = $request->headers->get(static::TOKEN_HEADER);
            }
            if (empty($apiKey)) {
                $apiKey = $request->get('apikey');
            }
            if (is_array($securityConfig['apikey'])) {
                return in_array($apiKey, $securityConfig['apikey']);
            } else {
                return $apiKey === $securityConfig['apikey'];
            }
        }

        return false;
    }
}
