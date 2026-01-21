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
import { type DynamicTypeOperatorRegistry } from '../graphql/operator-types/dynamic-type-operator-registry'
import { bundleServiceIds } from '../../config/service-ids'

export const DataHubModule: AbstractModule = {
  onInit: (): void => {
    const adapterRegistry = container.get<DynamicTypeDataHubAdapterRegistry>(bundleServiceIds['DataHub/DynamicTypes/Adapter/Registry'])
    adapterRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Adapter/GraphQL']))

    const operatorRegistry = container.get<DynamicTypeOperatorRegistry>(bundleServiceIds['DataHub/DynamicTypes/Operator/Registry'])
    operatorRegistry.registerDynamicType(container.get(bundleServiceIds['DataHub/DynamicTypes/Operator/DateFormatter']))

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
