/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { container, type AbstractModule } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { type MainNavRegistry } from '@pimcore/studio-ui-bundle/modules/app'
import { type WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { ConfigContainer } from './config-container'
import { type DynamicTypeDataHubAdapterRegistry } from './dynamic-types/dynamic-type-data-hub-adapter-registry'
import { type DynamicTypeOperatorRegistry } from '../operators/dynamic-type-operator-registry'
import { bundleServiceIds } from '../../config/service-ids'

export const DataHubModule: AbstractModule = {
  onInit: (): void => {
    const adapterRegistry = container.get<DynamicTypeDataHubAdapterRegistry>(bundleServiceIds['DataHub/DynamicTypes/Adapter/Registry'])
    adapterRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Adapter/GraphQL']))

    const operatorQueryRegistry = container.get<DynamicTypeOperatorRegistry>(bundleServiceIds['DataHub/DynamicTypes/Operator/GraphQL/QueryRegistry'])
    operatorQueryRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Operator/Alias']))
    operatorQueryRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Operator/Concatenator']))
    operatorQueryRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Operator/DateFormatter']))
    operatorQueryRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Operator/ElementCounter']))
    operatorQueryRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Operator/Substring']))
    operatorQueryRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Operator/Text']))
    operatorQueryRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Operator/Thumbnail']))
    operatorQueryRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Operator/ThumbnailHtml']))
    operatorQueryRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Operator/TranslateValue']))
    operatorQueryRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Operator/Trimmer']))

    const operatorMutationRegistry = container.get<DynamicTypeOperatorRegistry>(bundleServiceIds['DataHub/DynamicTypes/Operator/GraphQL/MutationRegistry'])
    operatorMutationRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Operator/IfEmpty']))
    operatorMutationRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Operator/LocaleCollector']))
    operatorMutationRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Operator/LocaleSwitcher']))

    // Register group icons
    operatorQueryRegistry.registerGroupConfig('formatter', { icon: { type: 'name', value: 'folder' }, priority: 10 })
    operatorQueryRegistry.registerGroupConfig('transformer', { icon: { type: 'name', value: 'folder' }, priority: 20 })
    operatorQueryRegistry.registerGroupConfig('other', { icon: { type: 'name', value: 'folder' }, priority: 30 })

    operatorMutationRegistry.registerGroupConfig('other', { icon: { type: 'name', value: 'folder' }, priority: 30 })

    const widgetRegistryService = container.get<WidgetRegistry>(serviceIds.widgetManager)
    const mainNavRegistryService = container.get<MainNavRegistry>(serviceIds.mainNavRegistry)

    widgetRegistryService.registerWidget({
      name: 'data-hub-configuration',
      component: ConfigContainer
    })

    mainNavRegistryService.registerMainNavItem({
      path: 'AutomationIntegration/DataHub',
      label: 'data-hub.configuration',
      order: 100,
      className: 'item-style-modifier',
      widgetConfig: {
        name: 'Data Hub Configuration',
        id: 'data-hub-configuration',
        component: 'data-hub-configuration',
        config: {
          translationKey: 'data-hub.configuration',
          icon: {
            type: 'name',
            value: 'database'
          }
        }
      }
    })
  }
}
