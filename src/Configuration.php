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

namespace Pimcore\Bundle\DataHubBundle;

use Pimcore\Bundle\DataHubBundle\Event\ConfigurationEvents;
use Pimcore\Model\AbstractModel;
use Pimcore\Model\User;
use Pimcore\Tool\Admin;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Configuration
 *
 * @method bool isWriteable()
 * @method Configuration\Dao getDao()
 *
 * @package Pimcore\Bundle\DataHubBundle
 */
class Configuration extends AbstractModel
{
    public const SECURITYCONFIG_AUTH_APIKEY = 'datahub_apikey';

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string|null
     */
    public $name;

    /**
     * @var string|null
     */
    public $group;

    /**
     * @var array|null
     */
    public $configuration;

    /**
     * @var int
     */
    protected $creationDate;

    /**
     * @var int
     */
    protected $modificationDate;

    /**
     * @param string|null $type
     * @param string|null $path
     * @param string|null $name
     * @param array|null $configuration
     */
    public function __construct($type, $path, $name = null, $configuration = null)
    {
        $type = $type ?: 'graphql';
        $this->setType($type);
        $this->setPath($path);
        $this->setName($name);
        $this->setConfiguration($configuration);
    }

    public function getObjectVars(): array
    {
        $data = parent::getObjectVars();

        $data['configuration']['general']['modificationDate'] = $this->modificationDate;
        $data['configuration']['general']['createDate'] = $this->creationDate;

        return $data['configuration'];
    }

    /**
     * @param int $creationDate
     *
     * @return self
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = (int) $creationDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param int $modificationDate
     *
     * @return self
     */
    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = (int) $modificationDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param array|null $configuration
     */
    public function setConfiguration($configuration): void
    {
        if ($configuration && empty($this->getName())) {
            $this->setName($configuration['configuration']['general']['name'] ?? null);
        }

        $this->configuration = $configuration;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        $data = $this->getObjectVars();
        $data['general']['writeable'] = $this->isWriteable();

        return $data;
    }

    /**
     * @param string|null $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function setGroup(?string $group): void
    {
        $this->group = $group;
    }

    public function getSqlObjectCondition(): ?string
    {
        return $this->configuration && $this->configuration['general'] ? $this->configuration['general']['sqlObjectCondition'] ?? null : null;
    }

    /**
     * @return string|bool
     */
    public function isActive()
    {
        return $this->configuration && $this->configuration['general'] ? ($this->configuration['general']['active'] ?? false) : false;
    }

    /**
     * @return string|bool
     */
    public function skipPermisssionCheck()
    {
        return $this->configuration['security']['skipPermissionCheck'] ?? false;
    }

    /**
     * @param string|null $path
     */
    public function setPath($path): void
    {
        $this->path = $path;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @throws \Exception
     */
    public function save(): void
    {
        if (!$this->isAllowed('update')) {
            throw new \Exception('Permissions missing to save the configuration');
        }

        $event = new GenericEvent($this);
        $event->setArgument('configuration', $this);
        \Pimcore::getEventDispatcher()->dispatch($event, ConfigurationEvents::CONFIGURATION_PRE_SAVE);

        if (empty($this->configuration)) {
            $this->configuration = [];
            $this->configuration['general'] = [];
        }

        if (!isset($this->configuration['workspaces'])) {
            $this->configuration['workspaces'] = [];
        }

        if (!isset($this->configuration['permissions'])) {
            $this->configuration['permissions']['user'] = [];
            $this->configuration['permissions']['role'] = [];
        }

        if (isset($this->configuration['general']['writeable'])) {
            unset($this->configuration['general']['writeable']);
        }

        if (empty($this->getPath())) {
            $this->setPath(null);
        }

        $this->configuration['general']['type'] = $this->type;
        $this->configuration['general']['path'] = $this->path;
        $this->configuration['general']['name'] = $this->name;

        $securityConfig = $this->getSecurityConfig();
        if (($this->configuration['general']['active'] ?? false) && isset($securityConfig['method']) && $securityConfig['method'] === self::SECURITYCONFIG_AUTH_APIKEY) {
            if (!is_array($securityConfig['apikey'])) {
                $securityConfig['apikey'] = [$securityConfig['apikey']];
            }

            foreach ($securityConfig['apikey'] as $apiKey) {
                if (strlen($apiKey) < 16) {
                    throw new \Exception('API key ' . $apiKey . ' does not satisfy the minimum length of 16 characters');
                }
            }
        }

        $this->configuration['workspaces'] = WorkspaceHelper::cleanupWorkspaces($this->configuration['workspaces']);

        // we need to recheck
        $this->getDao()->save();

        WorkspaceHelper::saveWorkspaces($this, $this->configuration['workspaces']);

        $event = new GenericEvent($this);
        $event->setArgument('configuration', $this);
        \Pimcore::getEventDispatcher()->dispatch($event, ConfigurationEvents::CONFIGURATION_POST_SAVE);
    }

    public function delete(): void
    {
        if (!$this->isAllowed('delete')) {
            throw new \Exception('Permissions missing to delete the configuration');
        }

        $this->getDao()->delete();

        $event = new GenericEvent($this);
        $event->setArgument('configuration', $this);
        \Pimcore::getEventDispatcher()->dispatch($event, ConfigurationEvents::CONFIGURATION_POST_DELETE);
    }

    /**
     * @return Configuration[]
     */
    public static function getList()
    {
        $config = new self(null, null);

        return $config->getDao()->loadList();
    }

    /**
     * @param string $name
     *
     * @return Configuration|null
     */
    public static function getByName($name): ?self
    {
        try {
            $config = new self(null, null);
            $config->getDao()->loadByName($name);

            return $config;
        } catch (\Pimcore\Model\Exception\NotFoundException $e) {
            return null;
        }
    }

    public function getQueryEntities(): array
    {
        $schema = $this->configuration['schema'] ?? null;
        $entities = $schema ? $schema['queryEntities'] : [];
        $entities = is_array($entities) ? array_keys($entities) : [];

        return $entities;
    }

    public function getSpecialEntities(): array
    {
        $schema = $this->configuration['schema'] ?? null;
        $entities = $schema ? $schema['specialEntities'] : [];

        return $entities;
    }

    public function getMutationEntities(): array
    {
        $schema = $this->configuration['schema'] ?? null;
        $entities = $schema ? $schema['mutationEntities'] : [];
        $entities = is_array($entities) ? array_keys($entities) : [];

        return $entities;
    }

    /**
     * @param string $entityName
     *
     * @return mixed
     */
    public function getQueryEntityConfig($entityName)
    {
        return isset($this->configuration['schema']['queryEntities'][$entityName]) ? $this->configuration['schema']['queryEntities'][$entityName] : null;
    }

    /**
     * @param string $entityName
     *
     * @return mixed
     */
    public function getMutationEntityConfig($entityName)
    {
        return $this->configuration['schema']['mutationEntities'][$entityName];
    }

    /**
     * @param string $entityName
     *
     * @return mixed
     */
    public function getQueryColumnConfig($entityName)
    {
        $config = $this->getQueryEntityConfig($entityName);

        return $config['columnConfig'] ?? null;
    }

    /**
     * @param string $entityName
     *
     * @return mixed
     */
    public function getMutationColumnConfig($entityName)
    {
        return $this->getMutationEntityConfig($entityName)['columnConfig'] ?? [];
    }

    /**
     * @return mixed
     */
    public function getSecurityConfig()
    {
        return $this->configuration['security'] ?? [];
    }

    /**
     * @return mixed
     */
    public function getPermissionsConfig()
    {
        return $this->configuration['permissions'] ?? [];
    }

    public function __clone(): void
    {
        if ($this->dao) {
            $this->dao = clone $this->dao;
            $this->dao->setModel($this);
        }
    }

    /**
     * @internal
     *
     * @param string $type
     * @param ?User $user
     *
     * @return bool
     */
    public function isAllowed(string $type, ?User $user = null)
    {
        if (null === $user) {
            $user = Admin::getCurrentUser();
        }

        if (!$user) {
            if (php_sapi_name() === 'cli') {
                return true;
            }

            return false;
        }

        //everything is allowed for admin
        if ($user->isAdmin() || $user->isAllowed('plugin_datahub_admin')) {
            return true;
        }

        /**
         * If there are no specific permissions set for this configuration then check the global adapter permission.
         * Otherwise, check the configured permissions only and ignore the global adapter permission.
         */
        $configKey = 'plugin_datahub_adapter_' . $this->getType();

        $permissionConfig = $this->getPermissionsConfig();
        $permissionSets = [];
        foreach ($permissionConfig['user'] ?? [] as $userConfig) {
            $permissionSets[$userConfig['name']] = $userConfig;
        }
        foreach ($permissionConfig['role'] ?? [] as $roleConfig) {
            $permissionSets[$roleConfig['name']] = $roleConfig;
        }

        if (empty($permissionSets)) {
            return $user->isAllowed($configKey);
        } else {
            if (isset($permissionSets[$user->getName()])) {
                return $permissionSets[$user->getName()][$type] ?? false;
            }
            foreach ($user->getRoles() as $roleId) {
                $role = User\Role::getById($roleId);
                if (isset($permissionSets[$role->getName()][$type]) && $permissionSets[$role->getName()][$type] === true) {
                    return $permissionSets[$role->getName()][$type];
                }
            }
        }

        return false;
    }
}
