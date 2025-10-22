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

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
#[AsCommand(
    name: 'datahub:configuration:rebuild-workspaces',
    description: 'Migrate workspaces from configuration files to database.'
)]
final class RebuildWorkspacesCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this->addOption(
            'configs',
            null,
            InputOption::VALUE_OPTIONAL,
            'Comma separated list of configurations'
        );
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $list = [];
        $options = $input->getOption('configs');
        if ($options) {
            $configs = explode(',', $options);
            foreach ($configs as $configFromOption) {
                $config = Configuration::getByName($configFromOption);
                if (!$config) {
                    $this->output->writeln('<error>Config ' . $configFromOption . ': Not found.</error>');
                } else {
                    $list[] = $config;
                }
            }
        } else {
            $list = Configuration::getList();
        }

        foreach ($list as $config) {
            $configName = $config->getName();
            $this->output->writeln("Config $configName: Processing ...");
            $workspaces = $config->getConfiguration()['workspaces'] ?? null;
            if (isset($workspaces) === true && count($workspaces) > 0) {
                $workspaces = WorkspaceHelper::cleanupWorkspaces($workspaces);
                WorkspaceHelper::saveWorkspaces($config, $workspaces);
                $this->output->writeln("Config $configName: Workspaces saved.");
            } else {
                $this->output->writeln("Config $configName: No workspaces found.");
            }
        }

        return Command::SUCCESS;
    }
}
