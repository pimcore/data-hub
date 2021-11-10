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

namespace Pimcore\Bundle\DataHubBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;

class Version20211110093612 extends AbstractPimcoreMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE `plugin_datahub_workspaces_document` MODIFY `configuration` VARCHAR(80);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // not needed
        $this->addSql('ALTER TABLE `plugin_datahub_workspaces_document` MODIFY `configuration` VARCHAR(80);');
    }
}
