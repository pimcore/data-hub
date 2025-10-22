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

namespace Pimcore\Bundle\DataHubBundle\Command\Configuration;

use Pimcore\Console\AbstractCommand;
use Pimcore\Model\Tool\SettingsStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
#[AsCommand(
    name: 'datahub:configuration:migrate-legacy-config',
    description: 'Migrate legacy configurations (datahub-configurations.php) to YAML or settings store, depending on your configuration.'
)]
final class MigrateLegacyConfig extends AbstractCommand
{
    protected function configure(): void
    {
        // Configuration moved to AsCommand attribute
    }

    private function loadLegacyConfigs(string $fileName): array
    {
        $file = \Pimcore\Config::locateConfigFile($fileName);
        $configs = [];

        if (file_exists($file)) {
            $configs = @include $file;
        }

        return $configs;
    }

    private function migrateToSettingsStore(string $id, string $scope, array $configs, bool $overwriteExistingConfig = false): void
    {
        if (count($configs) > 0) {
            $existingConfig = SettingsStore::get($id, $scope);
            if (!$existingConfig || $overwriteExistingConfig) {
                SettingsStore::set($id, json_encode($configs), 'string', $scope);
            }
        }
    }

    private function migrateConfiguration(string $fileName, string $scope): void
    {
        $configs = $this->loadLegacyConfigs($fileName);
        $configs = $configs['list'] ?? [];
        foreach ($configs as $key => $config) {
            $id = $config['general']['name'];
            $this->migrateToSettingsStore((string)$id, $scope, $config);
        }
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->migrateConfiguration('datahub-configurations.php', 'pimcore_data_hub');

        return Command::SUCCESS;
    }
}
