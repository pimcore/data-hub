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

class ImportService
{
    public function importConfigurationJson(string $json, array $allowedVars): Configuration
    {
        $importData = json_decode($json, true);
        $this->checkValidity($importData);

        $configuration = new Configuration(
            $importData['type'],
            $importData['path'],
            $importData['name'],
        );
        $configuration->setModificationDate(time());

        $configurationToImport = [];
        foreach ($allowedVars as $category => $categoryProperty) {
            foreach ($categoryProperty as $property) {
                if (isset($importData['configuration'][$category][$property])) {
                    $configurationToImport[$category][$property] = $importData['configuration'][$category][$property];
                }
            }
        }

        $configuration->setConfiguration($configurationToImport);
        $configuration->save();

        return $configuration;
    }

    /**
     * @throws \Exception
     */
    protected function checkValidity(array $configuration): void
    {
        if (!array_key_exists('type', $configuration) ||
            !array_key_exists('path', $configuration) ||
            !array_key_exists('name', $configuration)) {
            throw new \Exception('Required configuration keys ("type", "path" or "name") not found!');
        }

        $configuration = Configuration::getByName($configuration['name']);
        if ($configuration instanceof Configuration) {
            throw new \Exception('Name already exists.');
        }
    }
}
