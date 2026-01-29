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

namespace Pimcore\Bundle\DataHubBundle\Service\Studio;

use Exception;
use Pimcore\Bundle\DataHubBundle\ConfigEvents;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Event\AdminEvents;
use Pimcore\Bundle\DataHubBundle\Event\Config\SpecialEntitiesEvent;
use Pimcore\Bundle\DataHubBundle\Event\Studio\PreResponse\ConfigurationDetailEvent;
use Pimcore\Bundle\DataHubBundle\Event\Studio\PreResponse\ConfigurationEvent;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\Hydrator\ConfigurationDehydratorInterface;
use Pimcore\Bundle\DataHubBundle\Hydrator\ConfigurationDetailHydratorInterface;
use Pimcore\Bundle\DataHubBundle\Hydrator\ConfigurationHydratorInterface;
use Pimcore\Bundle\DataHubBundle\Model\SpecialEntitySetting;
use Pimcore\Bundle\DataHubBundle\Schema\Configuration as HydratedConfiguration;
use Pimcore\Bundle\DataHubBundle\Schema\ConfigurationDetail;
use Pimcore\Bundle\DataHubBundle\Utils\Constants\PermissionConstants;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\ElementExistsException;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\ForbiddenException;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\NotWriteableException;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\ValidationFailedException;
use Pimcore\Bundle\StudioBackendBundle\Security\Service\SecurityServiceInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/** @internal */
final readonly class ConfigurationService implements ConfigurationServiceInterface
{
    private const array REQUIRED_CREATE_PERMISSIONS = [
        PermissionConstants::PLUGIN_DATA_HUB_CONFIG,
        PermissionConstants::PLUGIN_DATA_HUB_ADMIN,
    ];

    private const array REQUIRED_READ_UPDATE_PERMISSIONS = [
        PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_READ,
        PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_UPDATE,
    ];

    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ConfigurationHydratorInterface $configurationHydrator,
        private ConfigurationDetailHydratorInterface $configurationDetailHydrator,
        private ConfigurationDehydratorInterface $configurationDehydrator,
        private Service $graphQlService,
        private SecurityServiceInterface $securityService,
        private ContainerBagInterface $parameterBag
    ) {
    }

    public function getConfigurations(): array
    {
        $hydratedConfigs = [];
        $configs = $this->resolveConfigurationList(Configuration::getList());

        foreach ($configs as $config) {
            if (
                !$config instanceof Configuration ||
                !$config->isAllowed(PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_READ)
            ) {
                continue;
            }

            $group = $config->getGroup();
            $hydratedGroupItem = $group && isset($hydratedConfigs[$group]) ? $hydratedConfigs[$group] : null;

            $hydratedItem = $this->configurationHydrator->hydrate(
                $config,
                $group,
                $hydratedGroupItem?->getChildren() ?? []
            );

            $this->eventDispatcher->dispatch(
                new ConfigurationEvent($hydratedItem),
                ConfigurationEvent::EVENT_NAME
            );

            $this->addHydratedConfiguration(
                $hydratedConfigs,
                $hydratedItem,
                $group
            );
        }

        return array_values($hydratedConfigs);
    }

    public function getConfiguration(string $name): ConfigurationDetail
    {
        $configuration = $this->fetchConfiguration($name);
        $config = $this->normalizeConfigurationSchema($configuration->getConfiguration());
        $config = $this->processSpecialEntities($config);

        $configuration->setConfiguration($config);

        $supportedQueryDataTypes = $this->graphQlService->getSupportedDataObjectQueryDataTypes();
        $supportedMutationDataTypes = $this->graphQlService->getSupportedDataObjectMutationDataTypes();

        $hydratedDetail = $this->configurationDetailHydrator->hydrate(
            $configuration,
            $supportedQueryDataTypes,
            $supportedMutationDataTypes
        );

        $this->eventDispatcher->dispatch(
            new ConfigurationDetailEvent($hydratedDetail),
            ConfigurationDetailEvent::EVENT_NAME
        );

        return $hydratedDetail;
    }

    public function addConfiguration(string $name, string $type, string $path): string
    {
        $this->ensureConfigurationsAreWriteable(
            PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_CREATE,
            'Cannot create configuration as configurations are not writeable.'
        );

        $this->checkUserPermission(self::REQUIRED_CREATE_PERMISSIONS);

        $this->ensureConfigDoesNotExist($name);

        $config = new Configuration($type, $path, $name);
        $config->save();

        return $name;
    }

    public function deleteConfiguration(string $name): void
    {
        $config = $this->fetchConfiguration($name);

        $this->ensureConfigurationIsWriteable(
            $config,
            PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_DELETE,
            'Cannot delete configuration "' . $name . '" as it is not writeable.'
        );

        $this->checkConfigPermission($config, PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_DELETE);

        WorkspaceHelper::deleteConfiguration($config);
        $config->delete();
    }

    public function cloneConfiguration(string $name, string $originalName): string
    {
        $this->ensureConfigurationsAreWriteable(
            PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_CREATE,
            'Cannot clone configuration as configurations are not writeable.'
        );

        $this->checkUserPermission(self::REQUIRED_CREATE_PERMISSIONS);

        $this->ensureConfigDoesNotExist($name);
        $originalConfig = $this->fetchConfiguration($originalName);

        $this->checkConfigPermission($originalConfig, PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_READ);
        $this->checkUserPermission(
            [
                PermissionConstants::PLUGIN_DATA_HUB_ADMIN,
                PermissionConstants::PLUGIN_DATA_HUB_ADAPTER_PREFIX . $originalConfig->getType(),
            ]
        );

        $clonedConfig = new Configuration(
            $originalConfig->getType(),
            $originalConfig->getPath(),
            $name,
            $originalConfig->getConfiguration()
        );

        $clonedConfig->save();

        return $name;
    }

    public function importConfiguration(string $json): array
    {
        $this->ensureConfigurationsAreWriteable(
            PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_CREATE,
            'Cannot import configuration as configurations are not writeable.'
        );

        $this->checkUserPermission(self::REQUIRED_CREATE_PERMISSIONS);

        $importData = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $this->validateUploadedConfigurationData($importData);

        $this->checkUserPermission(
            [
                PermissionConstants::PLUGIN_DATA_HUB_ADMIN,
                PermissionConstants::PLUGIN_DATA_HUB_ADAPTER_PREFIX . $importData['type'],
            ]
        );

        $configuration = new Configuration(
            $importData['type'],
            $importData['path'],
            $importData['name']
        );
        $configuration->setModificationDate(time());
        $configuration->setConfiguration($importData['configuration']);
        $configuration->save();

        return [
            'success' => true,
            'type' => $configuration->getType(),
            'name' => $configuration->getName(),
        ];
    }

    public function exportConfiguration(string $name): array
    {
        $configuration = $this->fetchConfiguration($name);

        $exportData = clone $configuration;
        $data = json_decode(
            json_encode($exportData, JSON_THROW_ON_ERROR),
            false,
            512,
            JSON_THROW_ON_ERROR
        );

        unset(
            $data->configuration->general->modificationDate,
            $data->configuration->general->createDate,
        );

        $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        $filename = sprintf(
            'datahub_%s_%s_export.json',
            $configuration->getType(),
            $configuration->getName()
        );

        return [
            'json' => $json,
            'filename' => $filename,
        ];
    }

    public function updateConfiguration(string $name, array $configuration, int $clientModificationDate): int
    {
        $config = $this->fetchConfiguration($name);

        $this->ensureConfigurationIsWriteable(
            $config,
            PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_UPDATE,
            'Cannot update configuration "' . $name . '" as it is not writeable.'
        );

        $this->checkConfigPermission($config, self::REQUIRED_READ_UPDATE_PERMISSIONS);

        $currentConfiguration = $config->getConfiguration();
        $savedModificationDate = 0;

        if ($currentConfiguration && isset($currentConfiguration['general']['modificationDate'])) {
            $savedModificationDate = $currentConfiguration['general']['modificationDate'];
        }

        if ($clientModificationDate < $savedModificationDate) {
            throw new ValidationFailedException(
                'The configuration was modified during editing, please reload the configuration and make your changes again'
            );
        }

        $configuration = $this->configurationDehydrator->dehydrate($configuration);
        $configuration['general']['modificationDate'] = time();

        $config->setConfiguration($configuration);
        $config->save();

        return $configuration['general']['modificationDate'];
    }

    private function checkConfigPermission(
        Configuration $configuration,
        array|string $permission
    ): void {
        $permissions = is_array($permission) ? $permission : [$permission];

        foreach ($permissions as $perm) {
            if (!$configuration->isAllowed($perm)) {
                throw new ForbiddenException('Permission denied: ' . $perm);
            }
        }
    }

    /**
     * Ensures that a specific configuration instance is writeable
     *
     * @throws NotWriteableException
     */
    private function ensureConfigurationIsWriteable(
        Configuration $configuration,
        string $permission,
        string $message
    ): void {
        if ($configuration->isWriteable() === false) {
            throw new NotWriteableException($permission, $message);
        }
    }

    /**
     * Ensures that configurations in general are writeable (for create operations)
     *
     * @throws NotWriteableException
     */
    private function ensureConfigurationsAreWriteable(
        string $permission,
        string $message
    ): void {
        if ((new Configuration(null, null))->isWriteable() === false) {
            throw new NotWriteableException($permission, $message);
        }
    }

    private function fetchConfiguration(string $name): Configuration
    {
        $configuration = Configuration::getByName($name);

        if (!$configuration instanceof Configuration) {
            throw new NotFoundHttpException('Datahub configuration ' . $name . ' does not exist.');
        }

        $this->checkConfigPermission($configuration, PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_READ);

        return $configuration;
    }

    private function ensureConfigDoesNotExist(string $name): void
    {
        $configuration = Configuration::getByName($name);
        if ($configuration instanceof Configuration) {
            throw new ElementExistsException(
                message: 'Configuration with name "' . $name . '" already exists.', 
                errorKey: 'data-hub.config-exists'
            );
        }
    }

    private function normalizeConfigurationSchema(array $config): array
    {
        $config['schema']['queryEntities'] = array_values($config['schema']['queryEntities'] ?? []);
        $config['schema']['mutationEntities'] = array_values($config['schema']['mutationEntities'] ?? []);
        $config['schema']['specialEntities'] = $config['schema']['specialEntities'] ?? [];

        return $config;
    }

    private function processSpecialEntities(array $config): array
    {
        $coreSettings = $this->buildCoreSpecialEntitySettings($config['schema']['specialEntities']);
        $specialSettingsEvent = new SpecialEntitiesEvent($coreSettings, $config);
        $this->eventDispatcher->dispatch($specialSettingsEvent, ConfigEvents::SPECIAL_ENTITIES);

        $config['schema']['specialEntities'] = $specialSettingsEvent->getSpecialSettings();

        return $config;
    }

    /**
     * @return SpecialEntitySetting[]
     */
    private function buildCoreSpecialEntitySettings(array $specialEntities): array
    {
        return [
            $this->createSpecialEntitySetting('document', true, true, true, true, $specialEntities),
            $this->createSpecialEntitySetting('document_folder', true, false, false, true, $specialEntities),
            $this->createSpecialEntitySetting('asset', true, true, true, true, $specialEntities),
            $this->createSpecialEntitySetting('asset_folder', true, true, true, true, $specialEntities),
            $this->createSpecialEntitySetting('asset_listing', true, true, true, true, $specialEntities),
            $this->createSpecialEntitySetting('object_folder', true, true, true, true, $specialEntities),
            $this->createTranslationSpecialEntitySetting('translation', $specialEntities),
            $this->createTranslationSpecialEntitySetting('translation_listing', $specialEntities),
        ];
    }

    private function createSpecialEntitySetting(
        string $name,
        bool $read,
        bool $create,
        bool $update,
        bool $delete,
        array $specialEntities
    ): SpecialEntitySetting {
        return new SpecialEntitySetting(
            $name,
            $read,
            $create,
            $update,
            $delete,
            $specialEntities[$name]['read'] ?? false,
            $specialEntities[$name]['create'] ?? false,
            $specialEntities[$name]['update'] ?? false,
            $specialEntities[$name]['delete'] ?? false
        );
    }

    private function createTranslationSpecialEntitySetting(
        string $name,
        array $specialEntities
    ): SpecialEntitySetting {
        return new SpecialEntitySetting(
            $name,
            true,
            false,
            false,
            false,
            $specialEntities['translation_listing']['read'] ?? false,
            $specialEntities['translation_listing']['create'] ?? false,
            $specialEntities['translation_listing']['update'] ?? false,
            $specialEntities['translation_listing']['delete'] ?? false
        );
    }

    private function resolveConfigurationList(array $configs): iterable
    {
        $event = new GenericEvent($this, ['list' => $configs]);
        $this->eventDispatcher->dispatch($event, AdminEvents::CONFIGURATION_LIST);

        return $event->getArgument('list');
    }

    private function addHydratedConfiguration(
        array &$hydratedConfigs,
        HydratedConfiguration $hydratedItem,
        ?string $group
    ): void {
        if ($group) {
            $hydratedConfigs[$group] = $hydratedItem;

            return;
        }

        $hydratedConfigs[] = $hydratedItem;
    }

    private function checkUserPermission(array|string $permission): void
    {
        $user = $this->securityService->getCurrentUser();
        $permissions = is_array($permission) ? $permission : [$permission];

        foreach ($permissions as $perm) {
            if (!$user->isAllowed($perm)) {
                throw new ForbiddenException('Permission denied: ' . $perm);
            }
        }
    }

    private function isBundleInstalled(?string $type): bool
    {
        try {
            $registeredBundles = $this->parameterBag->get('pimcore_data_hub');
        } catch (Exception) {
            return false;
        }

        return array_key_exists($type, $registeredBundles['supported_types']);
    }

    private function validateUploadedConfigurationData(array $importData): void
    {
        $type = $importData['type'] ?? null;
        $name = $importData['name'] ?? null;

        if (!isset($type, $importData['path'], $name)) {
            throw new ValidationFailedException(
                message: 'Uploaded configuration data is invalid. Missing keys: type, path, name.',
                errorKey: 'data-hub.import-config-invalid'
            );
        }

        $this->ensureConfigDoesNotExist($name);

        if (!$this->isBundleInstalled($type)) {
            throw new ValidationFailedException(sprintf(
                'Cannot handle type "%s". According bundle is not installed!',
                $type
            ));
        }
    }
}
