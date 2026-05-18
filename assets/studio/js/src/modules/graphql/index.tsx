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
import { type DynamicTypeOperatorRegistry } from '../operators/dynamic-type-operator-registry'
import { bundleServiceIds } from '../../config/service-ids'

export const GraphQLModule: AbstractModule = {
  onInit: (): void => {
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

    operatorQueryRegistry.registerGroupConfig('formatter', { icon: { type: 'name', value: 'formatters', colorToken: 'colorCodingRed2' }, priority: 10 })
    operatorQueryRegistry.registerGroupConfig('transformer', { icon: { type: 'name', value: 'transformers', colorToken: 'colorCodingViolet2' }, priority: 20 })
    operatorQueryRegistry.registerGroupConfig('other', { icon: { type: 'name', value: 'other-operators', colorToken: 'colorCodingBeige2' }, priority: 30 })

    operatorMutationRegistry.registerGroupConfig('other', { icon: { type: 'name', value: 'other-operators', colorToken: 'colorCodingBeige2' }, priority: 30 })
  }
}
