/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { uuid, isNonEmptyString } from '@pimcore/studio-ui-bundle/utils'
import { type TreeItemData, type TreePath } from './tree-item'
import { DropPosition } from '../../drag-types'

export function cloneItems (items: TreeItemData[]): TreeItemData[] {
  return items.map(item => ({
    ...item,
    key: item.key,
    attributes: {
      ...item.attributes,
      ...(Array.isArray(item.attributes.children)
        ? { children: cloneItems(item.attributes.children) }
        : {})
    }
  }))
}

export function ensureKeys (items: TreeItemData[]): TreeItemData[] {
  return items.map(item => ({
    ...item,
    key: isNonEmptyString(item.key) ? item.key : uuid(),
    attributes: {
      ...item.attributes,
      ...(Array.isArray(item.attributes.children)
        ? { children: ensureKeys(item.attributes.children) }
        : {})
    }
  }))
}

export function findItemPath (items: TreeItemData[], key: string, currentPath: TreePath = []): TreePath | null {
  for (let i = 0; i < items.length; i++) {
    const item = items[i]
    const itemPath = [...currentPath, i]

    if (item.key === key) {
      return itemPath
    }

    if (Array.isArray(item.attributes.children)) {
      const childPath = findItemPath(item.attributes.children, key, itemPath)
      if (childPath !== null) {
        return childPath
      }
    }
  }

  return null
}

export function getItemAtPath (items: TreeItemData[], path: TreePath): TreeItemData | null {
  if (path.length === 0) return null

  let current: TreeItemData | null = items[path[0]] ?? null

  for (let i = 1; i < path.length && current !== null; i++) {
    const children = current.attributes.children
    if (!Array.isArray(children)) return null
    current = children[path[i]] ?? null
  }

  return current
}

function getParentContext (items: TreeItemData[], path: TreePath): { parent: TreeItemData[] | null, index: number } {
  if (path.length === 0) return { parent: null, index: -1 }
  if (path.length === 1) return { parent: items, index: path[0] }

  const parentPath = path.slice(0, -1)
  const parent = getItemAtPath(items, parentPath)

  if (parent === null || !Array.isArray(parent.attributes.children)) {
    return { parent: null, index: -1 }
  }

  return { parent: parent.attributes.children, index: path[path.length - 1] }
}

export function insertAtPath (
  items: TreeItemData[],
  item: TreeItemData,
  path: TreePath,
  position: DropPosition = DropPosition.AFTER
): TreeItemData[] {
  const newItems = cloneItems(items)
  const itemWithKey: TreeItemData = {
    ...item,
    key: isNonEmptyString(item.key) ? item.key : uuid(),
    attributes: {
      ...item.attributes,
      ...(Array.isArray(item.attributes.children)
        ? { children: cloneItems(item.attributes.children) }
        : {})
    }
  }

  if (path.length === 0) {
    // Insert at end of top level
    newItems.push(itemWithKey)
    return newItems
  }

  if (position === DropPosition.INTO) {
    // Insert as child of item at path
    const target = getItemAtPath(newItems, path)
    if (target === null) return items

    if (!Array.isArray(target.attributes.children)) {
      target.attributes.children = []
    }
    target.attributes.children.push(itemWithKey)
    return newItems
  }

  // Insert as sibling
  const { parent, index } = getParentContext(newItems, path)
  if (parent === null || index === -1) return items

  const insertIndex = position === DropPosition.BEFORE ? index : index + 1
  parent.splice(insertIndex, 0, itemWithKey)

  return newItems
}

export function removeAtPath (
  items: TreeItemData[],
  path: TreePath
): { items: TreeItemData[], removed: TreeItemData | null } {
  if (path.length === 0) {
    return { items, removed: null }
  }

  const newItems = cloneItems(items)
  const { parent, index } = getParentContext(newItems, path)

  if (parent === null || index === -1 || index >= parent.length) {
    return { items, removed: null }
  }

  const [removed] = parent.splice(index, 1)
  return { items: newItems, removed }
}

export function moveItem (
  items: TreeItemData[],
  fromPath: TreePath,
  toPath: TreePath,
  position: DropPosition = DropPosition.AFTER
): TreeItemData[] {
  // First remove the item
  const { items: afterRemove, removed } = removeAtPath(items, fromPath)
  if (removed === null) return items

  // Adjust toPath if it was affected by the removal
  const adjustedToPath = adjustPathAfterRemoval(toPath, fromPath)

  // Insert at new location
  return insertAtPath(afterRemove, removed, adjustedToPath, position)
}

function adjustPathAfterRemoval (path: TreePath, removedPath: TreePath): TreePath {
  if (path.length === 0 || removedPath.length === 0) return path

  // Check if paths share a common ancestor
  const minLength = Math.min(path.length, removedPath.length)

  for (let i = 0; i < minLength - 1; i++) {
    if (path[i] !== removedPath[i]) {
      break
    }
  }

  // If they share a common parent (or are at same level) and removed was before target
  if (path.length >= removedPath.length) {
    const compareIndex = removedPath.length - 1
    if (
      removedPath.slice(0, compareIndex).every((v, i) => v === path[i]) &&
      removedPath[compareIndex] < path[compareIndex]
    ) {
      // Decrement the affected index
      const adjusted = [...path]
      adjusted[compareIndex]--
      return adjusted
    }
  }

  return path
}

export function collectAllKeys (items: TreeItemData[]): string[] {
  const keys: string[] = []

  const traverse = (children: TreeItemData[]): void => {
    for (const child of children) {
      keys.push(child.key)
      if (Array.isArray(child.attributes.children)) {
        traverse(child.attributes.children)
      }
    }
  }

  traverse(items)
  return keys
}
