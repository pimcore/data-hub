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

namespace Pimcore\Bundle\DataHubBundle\Event\Studio\PreResponse;

use Pimcore\Bundle\DataHubBundle\Schema\ConfigurationDetail;
use Pimcore\Bundle\StudioBackendBundle\Event\AbstractPreResponseEvent;

/**
 * Event fired before returning ConfigurationDetail objects in the API response.
 *
 * This event allows you to add additional attributes to the ConfigurationDetail schema
 * before it's serialized and sent to the client. This is useful for extending
 * the configuration detail information with custom data.
 *
 * Example usage in an EventSubscriber:
 *
 * ```php
 * use Pimcore\Bundle\DataHubBundle\Event\Studio\PreResponse\ConfigurationDetailEvent;
 * use Symfony\Component\EventDispatcher\EventSubscriberInterface;
 *
 * class CustomConfigurationDetailSubscriber implements EventSubscriberInterface
 * {
 *     public static function getSubscribedEvents(): array
 *     {
 *         return [
 *             ConfigurationDetailEvent::EVENT_NAME => 'onConfigurationDetail',
 *         ];
 *     }
 *
 *     public function onConfigurationDetail(ConfigurationDetailEvent $event): void
 *     {
 *         $configDetail = $event->getConfigurationDetail();
 *
 *         // Add custom attributes to the response
 *         $configDetail->addAdditionalAttribute('lastAccessedBy', 'john.doe');
 *         $configDetail->addAdditionalAttribute('usageCount', 42);
 *         $configDetail->addAdditionalAttribute('environment', 'production');
 *     }
 * }
 * ```
 */
final class ConfigurationDetailEvent extends AbstractPreResponseEvent
{
    public const string EVENT_NAME = 'pre_response.data_hub.configuration_detail';

    public function __construct(
        private readonly ConfigurationDetail $configurationDetail
    ) {
        parent::__construct($configurationDetail);
    }

    /**
     * Use this to get additional infos out of the response object
     */
    public function getConfigurationDetail(): ConfigurationDetail
    {
        return $this->configurationDetail;
    }
}
