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

final class Version20230503165847 extends AbstractMigration
{
    private const CONFIG_DIR = PIMCORE_CONFIGURATION_DIRECTORY . '/data_hub';

    public function getDescription(): string
    {
        return 'Replace childs with children in symfony configs';
    }

    public function up(Schema $schema): void
    {
        $this->replaceStringInFiles('childs:', 'children:');
    }

    public function down(Schema $schema): void
    {
        $this->replaceStringInFiles('children:', 'childs:');
    }

    private function replaceStringInFiles(string $search, string $replace): void
    {
        if (is_dir(self::CONFIG_DIR)) {
            $files = scandir(self::CONFIG_DIR, 0);
            for ($i = 2; $i < count($files); $i++) {
                $file = self::CONFIG_DIR . '/' . $files[$i];
                $fileContent = file_get_contents($file);
                $fileContent = str_replace($search, $replace, $fileContent);
                file_put_contents($file, $fileContent);
            }
        }
    }
}
