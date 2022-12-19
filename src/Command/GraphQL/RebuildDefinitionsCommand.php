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

namespace Pimcore\Bundle\DataHubBundle\Command\GraphQL;

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RebuildDefinitionsCommand extends AbstractCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('datahub:graphql:rebuild-definitions')
            ->setDescription('Rebuild GraphQL endpoint definitions')
            ->addOption(
                'definitions',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma separated list of endpoints'
            );
    }

    /**
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws \Exception
     *
     *@deprecated Use Pimcore\Bundle\DataHubBundle\Command\Configuration\RebuildWorkspacesCommand instead.
     *
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $included = [];
        if ($input->getOption('definitions')) {
            $included = $input->getOption('definitions');
            $included = explode(',', $included);
        } else {
            $list = Configuration::getList();
            foreach ($list as $configuration) {
                $endpoint = $configuration->getName();
                $included[] = $endpoint;
            }
        }

        foreach ($included as $endpoint) {
            $config = Configuration::getByName($endpoint);
            if (!$config) {
                $this->output->writeln('<error>Could not find config: ' . $endpoint . '</error>');
                continue;
            }

            $this->output->writeln('Save config: ' . $endpoint);

            $config->save();
        }

        $this->output->writeln('done');

        if (defined('Symfony\Component\Console\Command\Command::SUCCESS')) {
            return Command::SUCCESS;
        } else {
            //TODO remove this as soon as support for Symfony 4 gets dropped
            return 0;
        }
    }
}
