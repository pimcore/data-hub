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

namespace Pimcore\Bundle\DataHubBundle\Command\Configuration;

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RebuildWorkspacesCommand extends AbstractCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('datahub:configuration:rebuild-workspaces')
            ->setDescription('Migrate workspaces from configuration files to database.')
            ->addOption(
                'configs',
                null,
                InputOption::VALUE_OPTIONAL,
                'Comma separated list of configurations'
            );
    }

    /**
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     *
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
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

        if (defined('Symfony\Component\Console\Command\Command::SUCCESS')) {
            return Command::SUCCESS;
        } else {
            return 0;
        }
    }
}
