<?php

declare(strict_types = 1);

/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */

namespace Pimcore\Bundle\DataHubBundle\Configuration;

use Symfony\Component\Finder\Finder;

/**
 * Locates configs from path config/pimcore/headless-definitions.
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
