/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type DynamicTypeDataHubAdapterRegistry } from '../dynamic-types/dynamic-type-data-hub-adapter-registry'
import { isUndefined } from 'lodash'

/**
 * An adapter type the server does not offer cannot be created, whatever the permissions say. A
 * server that does not report its adapter types at all leaves every registered adapter available,
 * so an older backend keeps the behaviour it had before the setting existed.
 */
export const isAdapterEnabled = (
  adapterType: string,
  enabledAdapters: string[] | undefined
): boolean => enabledAdapters === undefined || enabledAdapters.includes(adapterType)

export const hasValidAdapter = (
  adapterType: string | undefined,
  adapterRegistry: DynamicTypeDataHubAdapterRegistry
): boolean => {
  if (isUndefined(adapterType)) return false

  return adapterRegistry.hasDynamicType(adapterType)
}
