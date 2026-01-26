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
import {
  DynamicTypeOperatorAlias,
  DynamicTypeOperatorConcatenator,
  DynamicTypeOperatorDateFormatter,
  DynamicTypeOperatorElementCounter,
  DynamicTypeOperatorIfEmpty,
  DynamicTypeOperatorLocaleCollector,
  DynamicTypeOperatorLocaleSwitcher,
  DynamicTypeOperatorSubstring,
  DynamicTypeOperatorText,
  DynamicTypeOperatorThumbnail,
  DynamicTypeOperatorThumbnailHtml,
  DynamicTypeOperatorTranslateValue,
  DynamicTypeOperatorTrimmer
} from './modules/operators/operators'

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

    // Query Operators
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/Alias'])).to(DynamicTypeOperatorAlias).inSingletonScope()
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/Concatenator'])).to(DynamicTypeOperatorConcatenator).inSingletonScope()
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/DateFormatter'])).to(DynamicTypeOperatorDateFormatter).inSingletonScope()
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/ElementCounter'])).to(DynamicTypeOperatorElementCounter).inSingletonScope()
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/Substring'])).to(DynamicTypeOperatorSubstring).inSingletonScope()
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/Text'])).to(DynamicTypeOperatorText).inSingletonScope()
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/Thumbnail'])).to(DynamicTypeOperatorThumbnail).inSingletonScope()
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/ThumbnailHtml'])).to(DynamicTypeOperatorThumbnailHtml).inSingletonScope()
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/TranslateValue'])).to(DynamicTypeOperatorTranslateValue).inSingletonScope()
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/Trimmer'])).to(DynamicTypeOperatorTrimmer).inSingletonScope()

    // Mutation Operators
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/IfEmpty'])).to(DynamicTypeOperatorIfEmpty).inSingletonScope()
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/LocaleCollector'])).to(DynamicTypeOperatorLocaleCollector).inSingletonScope()
    container.bind(String(bundleServiceIds['DataHub/DynamicTypes/Operator/LocaleSwitcher'])).to(DynamicTypeOperatorLocaleSwitcher).inSingletonScope()
  },

  // register modules here
  onStartup: ({ moduleSystem }): void => {
    moduleSystem.registerModule(DataHubModule)
  }
}
