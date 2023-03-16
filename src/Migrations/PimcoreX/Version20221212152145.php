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

final class Version20221212152145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Replace childs with children in configs';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE settings_store SET data=REPLACE(data, \'"childs":\', \'"children":\') WHERE scope=\'pimcore_data_hub\';');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE settings_store SET data=REPLACE(data, \'"children":\', \'"childs":\') WHERE scope=\'pimcore_data_hub\';');
    }
}
