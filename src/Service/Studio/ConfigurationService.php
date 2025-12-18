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

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Event\AdminEvents;
use Pimcore\Bundle\DataHubBundle\Event\Studio\PreResponse\ConfigurationEvent;
use Pimcore\Bundle\DataHubBundle\Hydrator\ConfigurationHydratorInterface;
use Pimcore\Bundle\DataHubBundle\Schema\Configuration as HydratedConfiguration;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\ForbiddenException;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\NotWriteableException;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/** @internal */
final readonly class ConfigurationService implements ConfigurationServiceInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ConfigurationHydratorInterface $configurationHydrator,
    ) {
    }

    public function getConfigurations(): array
    {
        $hydratedConfigs = [];
        $configs = $this->resolveConfigurationList(Configuration::getList());

        foreach ($configs as $config) {
            if (!$config instanceof Configuration) {
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
     * @throws \Exception
     */
    public function deleteConfiguration(string $name): void
    {
        $config = Configuration::getByName($name);

        if (!$config instanceof Configuration) {
            throw new NotFoundHttpException('Configuration does not exist.');
        }

        if ($config->isWriteable() === false) {
            throw new NotWriteableException(
                'delete',
                'Cant delete configuration "' . $name . '" as it is not writeable.'
            );
        }

        if (!$config->isAllowed('delete')) {
            throw new ForbiddenException('Permission denied to delete the configuration.');
        }

        WorkspaceHelper::deleteConfiguration($config);
        $config->delete();
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
}
