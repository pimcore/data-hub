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
import { isNil, flatMap } from 'lodash'
import { type InternalTreeNode, type TreePath } from '../tree-item/tree-item'
import { DropPosition } from '../../drag-types'

export function mapTree (
  items: InternalTreeNode[],
  transform: (item: InternalTreeNode) => InternalTreeNode
): InternalTreeNode[] {
  return items.map(item => {
    const transformed = transform(item)
    return {
      ...transformed,
      attributes: {
        ...transformed.attributes,
        ...(Array.isArray(item.attributes.children)
          ? { children: mapTree(item.attributes.children, transform) }
          : {})
      }
    }
  })
}

export function cloneItems (items: InternalTreeNode[]): InternalTreeNode[] {
  return mapTree(items, item => ({ ...item, attributes: { ...item.attributes } }))
}

export function ensureKeys (items: InternalTreeNode[]): InternalTreeNode[] {
  return mapTree(items, item => ({
    ...item,
    key: isNonEmptyString(item.key) ? item.key : uuid(),
    attributes: { ...item.attributes }
  }))
}

export function findItemPath (items: InternalTreeNode[], key: string, currentPath: TreePath = []): TreePath | null {
  for (let i = 0; i < items.length; i++) {
    const item = items[i]
    const itemPath = [...currentPath, i]

    if (item.key === key) {
      return itemPath
    }

    if (Array.isArray(item.attributes.children)) {
      const childPath = findItemPath(item.attributes.children, key, itemPath)
      if (!isNil(childPath)) {
        return childPath
      }
    }
  }

  return null
}

export function getItemAtPath (items: InternalTreeNode[], path: TreePath): InternalTreeNode | null {
  if (path.length === 0) return null

  let current: InternalTreeNode | null = items[path[0]] ?? null

  for (let i = 1; i < path.length && !isNil(current); i++) {
    const children = current.attributes.children
    if (!Array.isArray(children)) return null
    current = children[path[i]] ?? null
  }

  return current
}

function getParentContext (items: InternalTreeNode[], path: TreePath): { parent: InternalTreeNode[] | null, index: number } {
  if (path.length === 0) return { parent: null, index: -1 }
  if (path.length === 1) return { parent: items, index: path[0] }

  const parent = getItemAtPath(items, path.slice(0, -1))
  if (isNil(parent) || !Array.isArray(parent.attributes.children)) {
    return { parent: null, index: -1 }
  }

  return { parent: parent.attributes.children, index: path[path.length - 1] }
}

export function insertAtPath (
  items: InternalTreeNode[],
  item: InternalTreeNode,
  path: TreePath,
  position: DropPosition = DropPosition.AFTER
): InternalTreeNode[] {
  const newItems = cloneItems(items)
  const itemWithKey: InternalTreeNode = {
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
    newItems.push(itemWithKey)
    return newItems
  }

  if (position === DropPosition.INTO) {
    const target = getItemAtPath(newItems, path)
    if (isNil(target)) return items

    if (!Array.isArray(target.attributes.children)) {
      target.attributes.children = []
    }
    target.attributes.children.push(itemWithKey)
    return newItems
  }

  const { parent, index } = getParentContext(newItems, path)
  if (isNil(parent) || index === -1) return items

  const insertIndex = position === DropPosition.BEFORE ? index : index + 1
  parent.splice(insertIndex, 0, itemWithKey)

  return newItems
}

export function removeAtPath (
  items: InternalTreeNode[],
  path: TreePath
): { items: InternalTreeNode[], removed: InternalTreeNode | null } {
  if (path.length === 0) {
    return { items, removed: null }
  }

  const newItems = cloneItems(items)
  const { parent, index } = getParentContext(newItems, path)

  if (isNil(parent) || index === -1 || index >= parent.length) {
    return { items, removed: null }
  }

  const [removed] = parent.splice(index, 1)
  return { items: newItems, removed }
}

export function moveItem (
  items: InternalTreeNode[],
  fromPath: TreePath,
  toPath: TreePath,
  position: DropPosition = DropPosition.AFTER
): InternalTreeNode[] {
  const { items: afterRemove, removed } = removeAtPath(items, fromPath)
  if (isNil(removed)) return items

  // Adjust toPath if it was affected by the removal
  const adjustedToPath = adjustPathAfterRemoval(toPath, fromPath)

  return insertAtPath(afterRemove, removed, adjustedToPath, position)
}

export function adjustPathAfterRemoval (path: TreePath, removedPath: TreePath): TreePath {
  if (path.length === 0 || removedPath.length === 0) return path

  if (path.length >= removedPath.length) {
    const compareIndex = removedPath.length - 1
    if (
      removedPath.slice(0, compareIndex).every((v, i) => v === path[i]) &&
      removedPath[compareIndex] < path[compareIndex]
    ) {
      // Decrement the affected index since an earlier sibling was removed
      const adjusted = [...path]
      adjusted[compareIndex]--
      return adjusted
    }
  }

  return path
}

export function collectAllKeys (items: InternalTreeNode[]): string[] {
  return flatMap(items, item => {
    const children = item.attributes.children
    return [item.key, ...(Array.isArray(children) ? collectAllKeys(children) : [])]
  })
}
