<?php

declare(strict_types = 1);

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

namespace Pimcore\Bundle\DataHubBundle\Configuration;

use Symfony\Component\Finder\Finder;

/**
 * @deprecated will be removed in Data-Hub 2.0
 * Locates data hub configs
 */
class DatahubConfigLocator
{
    /**
     * Find config files for the given name (e.g. config)
     *
     * @param string $name
     * @param array $params
     *
     * @return array
     */
    public function locate(string $name, $params = [])
    {
        $result = [];
        $dirs = [];
        $finder = new Finder();

        if (is_dir(Dao::CONFIG_PATH)) {
            array_push($dirs, Dao::CONFIG_PATH);
        }

        if (empty($dirs)) {
            return [];
        }

        $finder
            ->files()
            ->in($dirs);

        foreach (['*.yml', '*.yaml'] as $namePattern) {
            $finder->name($namePattern);
        }

        foreach ($finder as $file) {
            $path = $file->getRealPath();
            if ($params['relativePath'] ?? false) {
                $path = $file->getRelativePathname();
            }

            $result[] = $path;
        }

        return $result;
    }
}
