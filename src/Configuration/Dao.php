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
use Pimcore\Bundle\DataHubBundle\Helper\PermissionsHelper;
use Pimcore\Config;
use Pimcore\File;
use Pimcore\Model;
use Symfony\Component\Uid\Uuid as Uid;

/**
 * Class Dao
 *
 * @package Pimcore\Bundle\DataHubBundle\Configuration
 */
class Dao extends Model\Dao\PimcoreLocationAwareConfigDao
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
     * @deprecated Will be removed in Pimcore 11
     */
    private const LEGACY_FILE = 'datahub-configurations.php';

    public const CONFIG_PATH = PIMCORE_CONFIGURATION_DIRECTORY . '/data-hub';

    public function configure()
    {
        $config = \Pimcore::getContainer()->getParameter('pimcore_data_hub');

        parent::configure([
            'containerConfig' => $config['configurations'] ?? [],
            'settingsStoreScope' => 'pimcore_data_hub',
            'storageDirectory' => self::CONFIG_PATH,
            'legacyConfigFile' => self::LEGACY_FILE,
            'writeTargetEnvVariableName' => 'PIMCORE_WRITE_TARGET_DATA_HUB'
        ]);
    }

    /**
     * save a configuration.
     */
    public function save(): void
    {
        if (!$this->model->getName()) {
            $this->model->getName(Uid::v4());
        }

        $ts = time();
        if (!$this->model->getCreationDate()) {
            $this->model->setCreationDate($ts);
        }
        $this->model->setModificationDate($ts);

        $data = $this->model->getObjectVars();
        $this->saveData($this->model->getName(), $data);
    }

    /**
     * delete a configuration.
     */
    public function delete(): void
    {
        $this->deleteData($this->model->getName());
    }

    public function setVariables($data)
    {
        $this->model->setConfiguration($data);
        $this->model->setName($data['general']['name'] ?? '');
        $this->model->setType($data['general']['type'] ?? '');
        $this->model->setPath($data['general']['path'] ?? '');
        $this->model->setModificationDate($data['general']['modificationDate'] ?? null);
        $this->model->setCreationDate($data['general']['createDate'] ?? null);
        $this->model->setGroup($data['general']['group'] ?? '');
    }

    /**
     * @internal
     *
     * gets a configuration by name.
     *
     * @param string $name
     *
     */
    public function loadByName($name)
    {
        $data = $this->getDataByName($name);

        if (!$data) {
            $data = $this->getDataByName('list');
            $data = $data[$name] ?? null;
        }
        if ($data) {
            $this->setVariables($data);
        } else {
            throw new Model\Exception\NotFoundException('Configuration with name: ' . $name . ' does not exist');
        }
    }

    /**
     * @deprecated Will be removed in Pimcore 11
     *
     * get a configuration by name.
     *
     * TODO: remove this static function and rename "loadByName" to "getByName"
     *
     * @param string $name
     *
     */
    public static function getByName($name)
    {
        try {
            $config = new Configuration(null, null);
            $config->getDao()->loadByName($name);

            return $config;
        } catch (\Pimcore\Model\Exception\NotFoundException $e) {
            return null;
        }
    }

    /**
     *
     * @deprecated will be removed with pimcore 11
     *
     * get latest modification date of configuration file.
     *
     * @return bool|int
     */
    public static function getConfigModificationDate()
    {
        return 0;
    }

    /**
     * get the whole configuration file content.
     *
     * @return array|mixed|null
     */
    private function &getConfig()
    {
        if (self::$_config) {
            return self::$_config;
        }
        $config = [];

        $list = $this->loadIdList();
        foreach ($list as $name) {
            $data = $this->getDataByName($name);
            if ($name === 'folders' and $this->dataSource === Config\LocationAwareConfigRepository::LOCATION_LEGACY) {
                unset($data[$name]);
            } elseif ($name === 'list' and $this->dataSource === Config\LocationAwareConfigRepository::LOCATION_LEGACY) {
                foreach ($data as $key => $legacyItem) {
                    $config[$key] = $legacyItem;
                }
            } else {
                $config[$name] = $data;
            }
        }

        self::$_config = $config;

        return self::$_config;
    }

    /**
     * get a default configuration.
     *
     * @return array
     */
    private static function defaultConfig(): array
    {
        return ['general' => [],
            'schema' => [],
            'security' => [],
            'workspaces' => []
        ];
    }

    /**
     * get the list of configurations.
     *
     * @return array
     */
    public function loadList(): array
    {
        $list = [];

        $configs = &$this->getConfig();
        foreach ($configs as $item) {
            $name = $item['general']['name'];
            $configuration = Configuration::getByName($name);
            if (PermissionsHelper::isAllowed($configuration, 'read')) {
                $list[$name] = $configuration;
            }
        }

        return $list;
    }

    /**
     * @deprecated Will be removed in Pimcore 11
     *
     * get the list of configurations.
     *
     * TODO: remove this static function and rename "loadList" to "getList"
     *
     * @return array
     *
     */
    public static function getList(): array
    {
        $configuration = new Configuration(null, null);

        return $configuration->getDao()->loadList();
    }

    /**
     * @param string $id
     * @param $data
     *
     * @return \array[][][]
     */
    protected function prepareDataStructureForYaml(string $id, $data)
    {
        return [
            'pimcore_data_hub' => [
                'configurations' => [
                    $id => $data,
                ],
            ],
        ];
    }
}
