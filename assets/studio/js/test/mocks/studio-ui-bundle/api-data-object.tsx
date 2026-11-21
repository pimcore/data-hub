/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { apiState, makeEndpoint } from './api-state'

export type GridColumnConfiguration = Record<string, any>
export type AdvancedColumnConfig = Record<string, any>

export const api = {
  endpoints: {
    dataObjectGetAvailableGridColumns: makeEndpoint('available', () => apiState.available),
    dataObjectGetGrid: makeEndpoint('grid', (arg) => apiState.grid(arg)),
    dataObjectGetGridPreview: makeEndpoint('gridPreview', (arg) => apiState.gridPreview(arg))
  }
}
