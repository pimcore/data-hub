<?php

declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Migrations\PimcoreX;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @internal
 */
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
