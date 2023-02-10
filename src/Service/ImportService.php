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
use Pimcore\Extension\Bundle\PimcoreBundleManager;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ImportService
{
    public function __construct(
        protected PimcoreBundleManager $bundleManager,
        protected ContainerBagInterface $parameterBag
    ) {
    }

    public function importConfigurationJson(string $json): Configuration
    {
        $importData = json_decode($json, true);
        $this->checkValidity($importData);

        $configuration = new Configuration(
            $importData['type'],
            $importData['path'],
            $importData['name']
        );
        $configuration->setModificationDate(time());
        $configuration->setConfiguration($importData['configuration']);
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

        if (!$this->isBundleInstalled($configuration['type'])) {
            throw new \Exception(sprintf(
                'Cant handle type "%s". Seems that the according bundle is not installed!',
                $configuration['type']
            ));
        }

        $configuration = Configuration::getByName($configuration['name']);
        if ($configuration instanceof Configuration) {
            throw new \Exception('Name already exists.');
        }
    }

    protected function isBundleInstalled(?string $type): bool
    {
        $registeredBundles = $this->parameterBag->get('pimcore_data_hub');

        return array_key_exists($type, $registeredBundles['supported_types']);
    }
}
