<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle;

use Pimcore\Bundle\DataHubBundle\Configuration\Dao;
use Pimcore\Model\AbstractModel;

class Configuration extends AbstractModel
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $name;

    /**
     * @var array|null
     */
    public $configuration;

    /**
     * Configuration constructor.
     *
     * @param $type
     * @param $path
     * @param null $name
     * @param null $configuration
     */
    public function __construct($type, $path, $name = null, $configuration = null)
    {
        $type = $type ? $type : 'graphql';
        $this->type = $type;
        $this->path = $path;
        $this->name = $name;
        $this->configuration = $configuration ? $configuration : [];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param mixed $configuration
     */
    public function setConfiguration($configuration)
    {
        if (is_array($configuration)) {
            $configuration = json_decode(json_encode($configuration), true);
        }
        $this->configuration = $configuration;
    }

    /**
     * @return mixed
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSqlObjectCondition()
    {
        return $this->configuration && $this->configuration['general'] ? $this->configuration['general']['sqlObjectListCondition'] : null;
    }

    /**
     * @return string
     */
    public function isActive()
    {
        return $this->configuration && $this->configuration['general'] ? $this->configuration['general']['active'] : false;
    }

    /**
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @throws \Exception
     */
    public function save()
    {
        if (empty($this->configuration)) {
            $this->configuration = [];
            $this->configuration['general'] = [];
        }

        if (empty($this->getPath())) {
            $this->setPath(null);
        }

        $this->configuration['general']['type'] = $this->type;
        $this->configuration['general']['path'] = $this->path;
        $this->configuration['general']['name'] = $this->name;

        $securityConfig = $this->getSecurityConfig();
        if ($this->configuration['general']['active'] && $securityConfig['method'] == 'datahub_apikey') {
            $apikey = $securityConfig['apikey'];
            if (strlen($apikey) < 16) {
                throw new \Exception('API key does not satisfy the minimum length of 16 characters');
            }
        }
        $this->getDao()->save();
        WorkspaceHelper::saveWorkspaces($this, $this->configuration["workspaces"]);
    }

    public function delete()
    {
        $this->getDao()->delete();
    }

    /**
     * @return mixed
     */
    public static function getList()
    {
        $config = new self(null);

        return $config->getDao()->getList();
    }

    /**
     * @param $name
     *
     * @return Configuration
     */
    public static function getByName($name)
    {
        return Dao::getByName($name);
    }

    /**
     * @return array
     */
    public function getQueryEntities()
    {
        $schema = $this->configuration['schema'];
        $entities = $schema ? $schema['queryEntities'] : [];
        $entities = array_keys($entities);

        return $entities;
    }

    /**
     * @return array
     */
    public function getSpecialEntities()
    {
        $schema = $this->configuration['schema'];
        $entities = $schema ? $schema['specialEntities'] : [];
        $entities = $entities;

        return $entities;
    }


    /**
     * @return array
     */
    public function getMutationEntities()
    {
        $schema = $this->configuration['schema'];
        $entities = $schema ? $schema['mutationEntities'] : [];
        $entities = array_keys($entities);

        return $entities;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getQueryEntityConfig($entityName)
    {
        return $this->configuration['schema']['queryEntities'][$entityName];
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getMutationEntityConfig($entityName)
    {
        return $this->configuration['schema']['mutationEntities'][$entityName];
    }

    /**
     * @param $entityName
     *
     * @return mixed
     */
    public function getQueryColumnConfig($entityName)
    {
        return $this->getQueryEntityConfig($entityName)['columnConfig'];
    }

    /**
     * @param $entityName
     *
     * @return mixed
     */
    public function getMutationColumnConfig($entityName)
    {
        return $this->getMutationEntityConfig($entityName)['columnConfig'];
    }


    /**
     * @return mixed
     */
    public function getSecurityConfig()
    {
        return $this->configuration['security'];
    }
}
