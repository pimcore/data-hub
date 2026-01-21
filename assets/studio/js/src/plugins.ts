/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type IAbstractPlugin } from '@pimcore/studio-ui-bundle'
import { DataHubModule } from './modules/config/index'
import { bundleServiceIds } from './config/service-ids'
import { DynamicTypeDataHubAdapterRegistry } from './modules/config/dynamic-types/dynamic-type-data-hub-adapter-registry'
import { DynamicTypeDataHubAdapterGraphQL } from './modules/config/dynamic-types/adapters/dynamic-type-data-hub-adapter-graphql'
import { DynamicTypeOperatorRegistry } from './modules/operators/dynamic-type-operator-registry'
import { DynamicTypeOperatorDateFormatter } from './modules/operators/operators/date-formatter/dynamic-type-operator-date-formatter'
import { DynamicTypeOperatorLocaleSwitcher } from './modules/operators/operators/locale-switcher/dynamic-type-operator-locale-switcher'

if (module.hot !== undefined) {
  module.hot.accept()
}

export const DataHubPlugin: IAbstractPlugin = {
  name: 'data-hub-plugin',

  // Register and overwrite services here
  onInit: ({ container }): void => {
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Adapter/Registry'])).to(DynamicTypeDataHubAdapterRegistry).inSingletonScope()
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Adapter/GraphQL'])).to(DynamicTypeDataHubAdapterGraphQL).inSingletonScope()

    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/GraphQL/QueryRegistry'])).to(DynamicTypeOperatorRegistry).inSingletonScope()
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/GraphQL/MutationRegistry'])).to(DynamicTypeOperatorRegistry).inSingletonScope()
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/DateFormatter'])).to(DynamicTypeOperatorDateFormatter).inSingletonScope()
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/LocaleSwitcher'])).to(DynamicTypeOperatorLocaleSwitcher).inSingletonScope()
  },

  // register modules here
  onStartup: ({ moduleSystem }): void => {
    moduleSystem.registerModule(DataHubModule)
  }
}
