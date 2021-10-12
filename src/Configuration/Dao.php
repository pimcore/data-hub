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
            $configItem = new Configuration($item['general']['type'], $item['general']['path'], $item['general']['name'], json_decode(json_encode($item), true));
            $configItem->setGroup($item['general']['group'] ?? null);
            $configurations[] = $configItem;
        }

        return $configurations;
    }
}
