/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { isNil } from 'lodash'
import { type InternalTreeNode, type TreePath, type TreeItem, createTreeItem } from '../tree-item/tree-item'
import { type DynamicTypeOperatorRegistry } from '../../../../../../../operators/dynamic-type-operator-registry'
import { findItemPath, getItemAtPath } from './tree-operations'
import { type DragInfo, DragType } from '../../drag-types'
import { createItemFromDragInfo } from './tree-conversion-utils'

interface SourceTreeItemInfo {
  item: TreeItem
  path: TreePath
}

function getSourceTreeItem (
  dragInfo: DragInfo,
  currentItems: InternalTreeNode[],
  operatorRegistry: DynamicTypeOperatorRegistry
): SourceTreeItemInfo | null {
  if (dragInfo.type !== DragType.TREE_ITEM || isNil(dragInfo.data.key)) {
    return null
  }

  const sourcePath = findItemPath(currentItems, String(dragInfo.data.key))
  if (isNil(sourcePath)) return null

  const sourceData = getItemAtPath(currentItems, sourcePath)
  if (isNil(sourceData)) return null

  return { item: createTreeItem(sourceData, operatorRegistry), path: sourcePath }
}

function getDraggedTreeItem (
  dragInfo: DragInfo,
  operatorRegistry: DynamicTypeOperatorRegistry
): TreeItem | null {
  const newItemData = createItemFromDragInfo(dragInfo)
  if (isNil(newItemData)) return null
  return createTreeItem(newItemData, operatorRegistry)
}

function isChildPath (childPath: TreePath, parentPath: TreePath): boolean {
  return childPath.length === parentPath.length + 1 &&
    childPath.slice(0, -1).every((v, i) => v === parentPath[i])
}

function hasSameParent (path1: TreePath, path2: TreePath): boolean {
  if (path1.length !== path2.length) return false
  return path1.slice(0, -1).every((v, i) => v === path2.slice(0, -1)[i])
}

export function validateDropIntoTarget (
  dragInfo: DragInfo,
  targetData: InternalTreeNode,
  targetPath: TreePath,
  currentItems: InternalTreeNode[],
  operatorRegistry: DynamicTypeOperatorRegistry
): boolean {
  const targetItem = createTreeItem(targetData, operatorRegistry)
  if (!targetItem.canHaveChildren()) return false

  const source = getSourceTreeItem(dragInfo, currentItems, operatorRegistry)
  if (!isNil(source)) {
    const isAlreadyChild = isChildPath(source.path, targetPath)
    return targetItem.canAcceptChild(source.item, isAlreadyChild)
  }

  const draggedItem = getDraggedTreeItem(dragInfo, operatorRegistry)
  return !isNil(draggedItem) && targetItem.canAcceptChild(draggedItem, false)
}

export function validateDropToParent (
  dragInfo: DragInfo,
  parentData: InternalTreeNode,
  targetPath: TreePath,
  currentItems: InternalTreeNode[],
  operatorRegistry: DynamicTypeOperatorRegistry
): boolean {
  const parentItem = createTreeItem(parentData, operatorRegistry)

  const source = getSourceTreeItem(dragInfo, currentItems, operatorRegistry)
  if (!isNil(source)) {
    if (hasSameParent(source.path, targetPath)) return true
    return parentItem.canAcceptChild(source.item, false)
  }

  const draggedItem = getDraggedTreeItem(dragInfo, operatorRegistry)
  return !isNil(draggedItem) && parentItem.canAcceptChild(draggedItem, false)
}
