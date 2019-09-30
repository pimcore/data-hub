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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\ApiPlatform\Metadata;

use Pimcore\Bundle\DataHubBundle\Configuration;

trait HubConfigTrait
{
    protected function getEntityName(string $resourceClass)
    {
        [$configName, $entity] = explode('_', substr($resourceClass, strlen('pimcore_data_hub_query_')));

        return $entity;
    }

    protected function getConfigName(string $resourceClass)
    {
        [$configName, $entity] = explode('_', substr($resourceClass, strlen('pimcore_data_hub_query_')));

        return $configName;
    }

    protected function getApiConfig(string $resourceClass)
    {
        [$configName, $entity] = explode('_', substr($resourceClass, strlen('pimcore_data_hub_query_')));

        return Configuration::getByName($configName);
    }

    protected function getEntityConfig(string $resourceClass, string $type = 'query')
    {
        [$configName, $entity] = explode('_', substr($resourceClass, strlen('pimcore_data_hub_query_')));
        $config = $this->getApiConfig($resourceClass);

        return $config->getConfiguration()['schema'][$type . 'Entities'][$entity];
    }
}
