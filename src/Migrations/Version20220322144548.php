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

namespace Pimcore\Bundle\DataHubBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Bundle\DataHubBundle\Configuration;

final class Version20211108160248 extends AbstractMigration
{
    private function migrateConfiguration(bool $up) {
        $configs = Configuration::getList();
        foreach($configs as $config) {
            $configuration = $config->getConfiguration();
            if($up === true) {
                $configuration["security"]["enableIntrospection"] = true;
            }
            else {
                unset($configuration["security"]["enableIntrospection"]);
            }
            $config->setConfiguration($configuration);
            $config->save();
        }
    }
    public function up(Schema $schema): void
    {
        $this->migrateConfiguration(true);
    }

    public function down(Schema $schema): void
    {
        $this->migrateConfiguration(false);
    }
}
