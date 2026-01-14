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

export const hasValidAdapter = (
  adapterType: string | undefined,
  adapterRegistry: DynamicTypeDataHubAdapterRegistry
): boolean => {
  if (isUndefined(adapterType)) return false

  try {
    const adapter = adapterRegistry.getDynamicType(adapterType, false)
    return !isUndefined(adapter)
  } catch (error) {
    return false
  }
}

export const getAdapterTypeString = (adapterType: string | undefined): string | undefined => {
  return !isUndefined(adapterType) ? String(adapterType) : undefined
}
