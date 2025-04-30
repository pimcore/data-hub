<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Service;

use Pimcore\Bundle\DataHubBundle\Configuration;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
final class CheckConsumerPermissionsService
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
                $apiKey = $request->query->getString('apikey');
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
