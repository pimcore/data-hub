/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type BundleDataHubConfiguration } from '../config-api-slice.gen'
import { isUndefined, isNil } from 'lodash'

export const findConfigById = (
  id: string,
  items: BundleDataHubConfiguration[] | undefined | null
): BundleDataHubConfiguration | null => {
  if (isNil(items) || !Array.isArray(items)) {
    return null
  }

  for (const item of items) {
    if (!isNil(item.id) && String(item.id) === String(id)) return item
    if (!isUndefined(item.children) && !isNil(item.children)) {
      const found = findConfigById(id, item.children)
      if (!isNil(found)) return found
    }
  }
  return null
}

export const findConfigInTree = (
  items: BundleDataHubConfiguration[] | undefined | null,
  predicate: (item: BundleDataHubConfiguration) => boolean
): BundleDataHubConfiguration | undefined => {
  if (isNil(items) || !Array.isArray(items)) {
    return undefined
  }

  for (const item of items) {
    if (predicate(item)) return item
    if (!isUndefined(item.children)) {
      const found = findConfigInTree(item.children, predicate)
      if (!isUndefined(found)) return found
    }
  }
  return undefined
}

export const filterConfigsRecursive = (
  items: BundleDataHubConfiguration[] | undefined | null,
  searchValue: string
): BundleDataHubConfiguration[] => {
  if (isNil(items) || !Array.isArray(items)) {
    return []
  }

  return items.reduce<BundleDataHubConfiguration[]>((acc, item) => {
    const matchesSearch = item.text.toLowerCase().includes(searchValue.toLowerCase())
    const filteredChildren = !isUndefined(item.children)
      ? filterConfigsRecursive(item.children, searchValue)
      : []

    if (matchesSearch || filteredChildren.length > 0) {
      acc.push({
        ...item,
        children: filteredChildren.length > 0 ? filteredChildren : item.children
      })
    }

    return acc
  }, [])
}
