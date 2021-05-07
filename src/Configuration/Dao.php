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

namespace Pimcore\Bundle\DataHubBundle\Configuration;

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Config;
use Pimcore\File;
use Pimcore\Model\Dao\AbstractDao;

/**
 * Class Dao
 *
 * @package Pimcore\Bundle\DataHubBundle\Configuration
 */
class Dao extends AbstractDao
{
    public const ROOT_PATH = '/';

    /**
     * path to the configuration file
     */
    public const CONFIG_FILE = 'datahub-configurations.php';

    /**
     * @var null|array
     */
    private static $_config = null;

    /**
     * save a configuration.
     */
    public function save(): void
    {
        $name = $this->model->getName();
        $config = &self::getConfig();

        $config['list'][$name] = json_decode(json_encode($this->model->getConfiguration()), true);

        self::writeConfig($config);
    }

    /**
     * delete a configuration.
     */
    public function delete(): void
    {
        $name = $this->model->getName();
        $config = & self::getConfig();

        unset($config['list'][$name]);

        self::writeConfig($config);
    }

    /**
     * get a configuration by name.
     *
     * @param string $name
     *
     * @return Configuration|null
     */
    public static function getByName($name): ?Configuration
    {
        $list = self::getList();

        foreach ($list as $item) {
            if ($item->getName() === $name) {
                return $item;
            }
        }

        return null;
    }

    /**
     * get all folders.
     *
     * @return mixed
     */
    public static function getFolders()
    {
        $config = &self::getConfig();

        return $config['folders'];
    }

    /**
     * get a folder by path.
     *
     * @param $path
     *
     * @return mixed|null
     */
    public static function getFolderByPath($path)
    {
        $folders = self::getFolders();

        if (!empty($folders[$path])) {
            return $folders[$path];
        }

        return null;
    }

    /**
     * add a folder.
     *
     * @param $parent
     * @param $name
     * @param bool $save
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function addFolder($parent, $name, $save = true): array
    {
        if (!$parent) {
            $parent = null;
        }

        $path = (!$parent ? self::ROOT_PATH : $parent) . $name . '/';

        if (self::getFolderByPath($path)) {
            throw new \Exception('directory already exists.');
        }

        $config = & self::getConfig();

        $folder = [
            'parent' => $parent,
            'path' => $path,
            'name' => $name
        ];

        $config['folders'][$path] = $folder;

        if ($save) {
            self::writeConfig($config);
        }

        return $folder;
    }

    /**
     * delete a folder by path.
     *
     * @param $path
     */
    public static function deleteFolder($path): void
    {
        $config = & self::getConfig();

        self::deleteFolderRec($config, $path);

        self::writeConfig($config);
    }

    /**
     * @param $config
     * @param $path
     */
    private static function deleteFolderRec(&$config, $path): void
    {
        if (!empty($config['folders'][$path])) {
            unset($config['folders'][$path]);

            foreach ($config['list'] as $key => $item) {
                if ($item['general']['path'] === $path) {
                    unset($config['list'][$key]);
                }
            }

            foreach ($config['folders'] as $folder) {
                if ($folder['parent'] === $path) {
                    self::deleteFolderRec($config, $folder['path']);
                }
            }
        }
    }

    /**
     * @param $who
     * @param $to
     *
     * @throws \Exception
     */
    public static function moveConfiguration($who, $to): void
    {
        $configuration = self::getByName($who);

        if (!$configuration) {
            return;
        }

        $configuration->setPath($to);

        $configuration->save();
    }

    /**
     * @param $who
     * @param $to
     *
     * @throws \Exception
     */
    public static function moveFolder($who, $to): void
    {
        self::moveFolderRec($who, $to);

        $config = & self::getConfig();
        self::writeConfig($config);
    }

    /**
     * @param $who
     * @param $to
     *
     * @throws \Exception
     */
    private static function moveFolderRec($who, $to): void
    {
        $config = & self::getConfig();

        if (empty($config['folders'][$who])) {
            return;
        }

        $folder = $config['folders'][$who];

        unset($config['folders'][$who]);

        $now = self::addFolder($to, $folder['name'], false);

        foreach ($config['list'] as &$item) {
            if ($item['general']['path'] === $who) {
                $item['general']['path'] = $now['path'];
            }
        }

        self::moveSubfolders($who, $now['path']);
    }

    /**
     * @param $old
     * @param $now
     *
     * @throws \Exception
     */
    private static function moveSubfolders($old, $now): void
    {
        $config = & self::getConfig();

        foreach ($config['folders'] as $folder) {
            if ($folder['parent'] === $old) {
                self::moveFolderRec($folder['path'], $now);
            }
        }
    }

    /**
     * get the list of configurations.
     *
     * @return array
     */
    public static function getList(): array
    {
        $config = & self::getConfig();
        $configurations = [];

        foreach ($config['list'] as $item) {
            $configurations[] = new Configuration($item['general']['type'], $item['general']['path'], $item['general']['name'], json_decode(json_encode($item), true));
        }

        return $configurations;
    }

    /**
     * get latest modification date of configuration file.
     *
     * @return bool|int
     */
    public static function getConfigModificationDate()
    {
        $config = Config::locateConfigFile(self::CONFIG_FILE);

        if (!file_exists($config)) {
            return false;
        }

        return filemtime($config);
    }

    /**
     * get the whole configuration file content.
     *
     * @return array|mixed|null
     */
    private static function &getConfig()
    {
        if (self::$_config) {
            return self::$_config;
        }

        $file = Config::locateConfigFile(self::CONFIG_FILE);
        $config = null;

        if (!file_exists($file)) {
            $config = self::defaultConfig();

            self::writeConfig($config);
        } else {
            $config = include($file);
        }

        self::$_config = $config;

        return self::$_config;
    }

    /**
     * write the configuration file.
     *
     * @param $config
     */
    private static function writeConfig($config): void
    {
        File::putPhpFile(Config::locateConfigFile(self::CONFIG_FILE), to_php_data_file_format($config));
    }

    /**
     * get a default configuration.
     *
     * @return array
     */
    private static function defaultConfig(): array
    {
        return [
            'folders' => [],
            'list' => []
        ];
    }
}
