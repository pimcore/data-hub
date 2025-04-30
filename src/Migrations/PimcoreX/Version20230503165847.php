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
