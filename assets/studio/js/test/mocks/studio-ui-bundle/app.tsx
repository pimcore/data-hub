/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

export const useTranslation = (): { t: (key: string) => string } => ({ t: (key: string) => key })

export const serviceIds = { 'DynamicTypes/AdvancedGridCellRegistry': 'advanced-grid-cell-registry' }

// The field definition types the fixtures use are the known ones
const KNOWN_FIELDTYPES = new Set(['input', 'classificationstore'])
const registry = {
  hasDynamicType: () => false,
  getDynamicType: (name: string) => KNOWN_FIELDTYPES.has(name) ? {} : undefined
}

// One instance for every call, as the container hands out one registry
export const useInjection = (): typeof registry => registry
