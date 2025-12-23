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
use Pimcore\Bundle\DataHubBundle\Event\Studio\PreResponse\ConfigurationEvent;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
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
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/** @internal */
final readonly class ConfigurationService implements ConfigurationServiceInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ConfigurationHydratorInterface $configurationHydrator,
        private ConfigurationDetailHydratorInterface $configurationDetailHydrator,
        private Service $graphQlService,
        private SecurityServiceInterface $securityService,
        protected ContainerBagInterface $parameterBag
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
            ){
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

    /**
     * @throws Exception
     */
    public function getConfiguration(string $name): ConfigurationDetail
    {
        $configuration = $this->fetchConfiguration($name);
        $config = $this->normalizeConfigurationSchema($configuration->getConfiguration());
        $config = $this->processSpecialEntities($config);

        $configuration->setConfiguration($config);

        $supportedQueryDataTypes = $this->graphQlService->getSupportedDataObjectQueryDataTypes();
        $supportedMutationDataTypes = $this->graphQlService->getSupportedDataObjectMutationDataTypes();

        return $this->configurationDetailHydrator->hydrate(
            $configuration,
            $supportedQueryDataTypes,
            $supportedMutationDataTypes
        );
    }

    /**
     * @throws Exception
     */
    public function addConfiguration(string $name, string $type, string $path): string
    {
        if ((new Configuration(null, null))->isWriteable() === false) {
            throw new NotWriteableException(
                PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_CREATE,
                'Cannot create configuration as configurations are not writeable.'
            );
        }

        $this->checkUserPermission(
            [
                PermissionConstants::PLUGIN_DATA_HUB_CONFIG,
                PermissionConstants::PLUGIN_DATA_HUB_ADMIN
            ]
        );

        if ($this->configExists($name)) {
            throw new ElementExistsException('Configuration with name "' . $name . '" already exists.');
        }

        $config = new Configuration($type, $path, $name);
        $config->save();

        return $name;
    }

    /**
     * @throws Exception
     */
    public function deleteConfiguration(string $name): void
    {
        $config = $this->fetchConfiguration($name);

        if ($config->isWriteable() === false) {
            throw new NotWriteableException(
                PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_DELETE,
                'Cant delete configuration "' . $name . '" as it is not writeable.'
            );
        }

        $this->checkConfigPermission($config, PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_DELETE);

        WorkspaceHelper::deleteConfiguration($config);
        $config->delete();
    }

    /**
     * @throws Exception
     */
    public function cloneConfiguration(string $name, string $originalName): string
    {
        if ((new Configuration(null, null))->isWriteable() === false) {
            throw new NotWriteableException(
                PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_CREATE,
                'Cannot clone configuration as configurations are not writeable.'
            );
        }

        $this->checkUserPermission(
            [
                PermissionConstants::PLUGIN_DATA_HUB_CONFIG,
                PermissionConstants::PLUGIN_DATA_HUB_ADMIN
            ]
        );

        if ($this->configExists($name)) {
            throw new ElementExistsException('Configuration with name "' . $name . '" already exists.');
        }

        $originalConfig = $this->fetchConfiguration($originalName);

        $this->checkConfigPermission($originalConfig, PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_READ);
        $this->checkUserPermission(
            [
                PermissionConstants::PLUGIN_DATA_HUB_ADMIN,
                PermissionConstants::PLUGIN_DATA_HUB_ADAPTER_PREFIX . $originalConfig->getType()
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

    /**
     * @throws Exception
     */
    public function importConfiguration(string $json): array
    {
        if ((new Configuration(null, null))->isWriteable() === false) {
            throw new NotWriteableException(
                PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_CREATE,
                'Cannot import configuration as configurations are not writeable.'
            );
        }

        $this->checkUserPermission(
            [
                PermissionConstants::PLUGIN_DATA_HUB_CONFIG,
                PermissionConstants::PLUGIN_DATA_HUB_ADMIN
            ]
        );

        $importData = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $this->validateUploadedConfigurationData($importData);

        $this->checkUserPermission(
            [
                PermissionConstants::PLUGIN_DATA_HUB_ADMIN,
                PermissionConstants::PLUGIN_DATA_HUB_ADAPTER_PREFIX . $importData['type']
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

    /**
     * @throws Exception
     */
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

    private function checkConfigPermission(
        Configuration $configuration,
        array|string $permission
    ): void {
        $throw = false;

        if(is_string($permission) && !$configuration->isAllowed($permission)) {
            $throw = true;
        }

        if(is_array($permission)) {
            foreach($permission as $perm) {
                if (!$configuration->isAllowed($perm)) {
                    $throw = true;
                    break;
                }
            }
        }

        if($throw) {
            throw new ForbiddenException('Permission denied: ' . $permission);
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

    private function configExists(string $name): bool
    {
        $configuration = Configuration::getByName($name);
        return $configuration instanceof Configuration;
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
        $throw = false;

        if(is_string($permission) && !$user->isAllowed($permission)) {
            $throw = true;
        }

        if(is_array($permission)) {
            foreach($permission as $perm) {
                if (!$user->isAllowed($perm)) {
                    $throw = true;
                }
            }
        }

        if($throw) {
            throw new ForbiddenException('Permission denied: ' . $permission);
        }
    }

    private function isBundleInstalled(?string $type): bool
    {
        try {
            $registeredBundles = $this->parameterBag->get('pimcore_data_hub');
        }
        catch(Exception) {
            return false;
        }

        return array_key_exists($type, $registeredBundles['supported_types']);
    }

    private function validateUploadedConfigurationData(array $importData): void
    {
        $type = $importData['type'] ?? null;
        $name = $importData['name'] ?? null;

        if (!isset($type) || !isset($importData['path']) || !isset($name)) {
            throw new ValidationFailedException(
                "Uploaded configuration data is invalid. Missing keys: type, path, name."
            );
        }

        if ($this->configExists($name)) {
            throw new ElementExistsException('Configuration with name "' . $name . '" already exists.');
        }

        if (!$this->isBundleInstalled($type)) {
            throw new ValidationFailedException(sprintf(
                'Cant handle type "%s". According bundle is not installed!',
                $type
            ));
        }
    }
}
