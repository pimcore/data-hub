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
use Pimcore\Model;
use Symfony\Component\Uid\Uuid as Uid;

/**
 * Class Dao
 *
 * @package Pimcore\Bundle\DataHubBundle\Configuration
 *
 * @property Configuration $model
 *
 * @internal
 */
final class Dao extends Model\Dao\PimcoreLocationAwareConfigDao
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

    public function configure(): void
    {
        $config = \Pimcore::getContainer()->getParameter('pimcore_data_hub');

        $storageConfig = $config['config_location']['data_hub'];
        parent::configure([
            'containerConfig' => $config['configurations'] ?? [],
            'settingsStoreScope' => 'pimcore_data_hub',
            'storageConfig' => $storageConfig,
        ]);

    }

    /**
     * save a configuration.
     */
    public function save(): void
    {
        if (!$this->model->getName()) {
            $this->model->setName(Uid::v4());
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

    /**
     * @param array $data
     *
     * @return void
     */
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
    public function getByName($name)
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
     * get the whole configuration file content.
     *
     * @return array
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
            if ($this->dataSource !== Config\LocationAwareConfigRepository::LOCATION_SETTINGS_STORE
                && $this->dataSource !== Config\LocationAwareConfigRepository::LOCATION_SYMFONY_CONFIG) {
                if ($name === 'folders') {
                    unset($data[$name]);
                } else {
                    foreach ($data as $key => $legacyItem) {
                        $config[$key] = $legacyItem;
                    }
                }
            } else {
                $config[$name] = $data;
            }
        }

        self::$_config = $config;

        return self::$_config;
    }

    /**
     * get the list of configurations.
     *
     */
    public function getList(): array
    {
        $list = [];

        $configs = &$this->getConfig();
        foreach ($configs as $item) {
            $name = $item['general']['name'];
            $configuration = Configuration::getByName($name);
            $list[$name] = $configuration;
        }

        return $list;
    }

    /**
     * @param mixed $data
     *
     * @return array[][][]
     */
    protected function prepareDataStructureForYaml(string $id, $data): mixed
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
