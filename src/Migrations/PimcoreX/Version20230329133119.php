<?php

declare(strict_types=1);

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

namespace Pimcore\Bundle\DataHubBundle\Migrations\PimcoreX;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230329133119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'rename default dir for symfony-config files';
    }

    public function up(Schema $schema): void
    {
        $this->renameConfigFolder('data-hub', '-', '_');
    }

    public function down(Schema $schema): void
    {
        $this->renameConfigFolder('data_hub', '_', '-');
    }

    private function renameConfigFolder(string $folder, string $search, string $replace): void
    {
        $configDir = \Pimcore::getContainer()->getParameter('kernel.project_dir') . '/var/config/';
        if (is_dir($configDir . $folder)) {
            rename($configDir . $folder, $configDir . str_replace($search, $replace, $folder));
        }
    }
}
