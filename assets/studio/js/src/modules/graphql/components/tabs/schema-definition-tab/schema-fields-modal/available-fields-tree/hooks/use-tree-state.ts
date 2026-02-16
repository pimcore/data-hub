/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useCallback, useState, useEffect, useRef } from 'react'
import { isNil } from 'lodash'
import { type QueryEntityConfig } from '../../types'
import { type InternalTreeNode, type TreePath } from '../tree-item/tree-item'
import {
  ensureKeys,
  findItemPath,
  getItemAtPath,
  insertAtPath,
  removeAtPath,
  adjustPathAfterRemoval,
  mapTree
} from '../utils/tree-operations'
import { type DynamicTypeOperatorRegistry } from '../../../../../../../operators/dynamic-type-operator-registry'
import { DragType, DropPosition, type DragInfo } from '../../drag-types'
import { persistedColumnsToInternalNodes, internalNodesToPersistedColumns } from '../utils/tree-conversion-utils'
import { validateDropIntoTarget, validateDropToParent } from '../utils/tree-validation-utils'

interface UseTreeStateProps {
  entityConfig?: QueryEntityConfig
  onEntityConfigChange: (config: QueryEntityConfig) => void
  operatorRegistry: DynamicTypeOperatorRegistry
}

interface UseTreeStateReturn {
  items: InternalTreeNode[]
  updateItems: (items: InternalTreeNode[]) => void
  updateItemAttributes: (key: string, attributes: Record<string, any>) => void
  findPath: (key: string) => TreePath | null
  getItem: (path: TreePath) => InternalTreeNode | null
  deleteByKey: (key: string) => void
  insert: (item: InternalTreeNode, targetPath: TreePath, position: DropPosition) => void
  appendToRoot: (item: InternalTreeNode) => void
  move: (sourceKey: string, targetPath: TreePath, position: DropPosition) => void
  canDrop: (dragInfo: DragInfo, targetKey: string, position: DropPosition) => boolean
  canDropToRoot: (dragInfo: DragInfo) => boolean
}

export const useTreeState = ({
  entityConfig,
  onEntityConfigChange,
  operatorRegistry
}: UseTreeStateProps): UseTreeStateReturn => {
  const isInternalUpdate = useRef(false)

  const [items, setItems] = useState<InternalTreeNode[]>(() => {
    const externalColumns = entityConfig?.columnConfig?.columns ?? []
    return ensureKeys(persistedColumnsToInternalNodes(externalColumns))
  })

  const itemsRef = useRef(items)
  itemsRef.current = items

  // Sync from entityConfig when it changes externally
  useEffect(() => {
    if (isInternalUpdate.current) {
      isInternalUpdate.current = false
      return
    }

    const externalColumns = entityConfig?.columnConfig?.columns ?? []
    setItems(ensureKeys(persistedColumnsToInternalNodes(externalColumns)))
  }, [entityConfig])

  const updateItems = useCallback((newItems: InternalTreeNode[]): void => {
    isInternalUpdate.current = true
    itemsRef.current = newItems
    setItems(newItems)

    const columns = internalNodesToPersistedColumns(newItems)
    const updatedConfig = {
      ...entityConfig,
      columnConfig: {
        ...entityConfig?.columnConfig,
        columns
      }
    }
    onEntityConfigChange(updatedConfig as QueryEntityConfig)
  }, [entityConfig, onEntityConfigChange])

  const findPath = useCallback((key: string): TreePath | null => {
    return findItemPath(items, key)
  }, [items])

  const getItem = useCallback((path: TreePath): InternalTreeNode | null => {
    return getItemAtPath(items, path)
  }, [items])

  const deleteByKey = useCallback((key: string): void => {
    const path = findItemPath(items, key)
    if (isNil(path)) return

    const { items: newItems } = removeAtPath(items, path)
    updateItems(newItems)
  }, [items, updateItems])

  const insert = useCallback((
    item: InternalTreeNode,
    targetPath: TreePath,
    position: DropPosition
  ): void => {
    const newItems = insertAtPath(items, item, targetPath, position)
    updateItems(newItems)
  }, [items, updateItems])

  const appendToRoot = useCallback((item: InternalTreeNode): void => {
    const newItems = [...items, item]
    updateItems(newItems)
  }, [items, updateItems])

  const updateItemAttributes = useCallback((key: string, attributes: Record<string, any>): void => {
    const newItems = mapTree(items, item =>
      item.key === key
        ? { ...item, attributes: { ...item.attributes, ...attributes } }
        : item
    )
    updateItems(newItems)
  }, [items, updateItems])

  const move = useCallback((
    sourceKey: string,
    targetPath: TreePath,
    position: DropPosition
  ): void => {
    const sourcePath = findItemPath(items, sourceKey)
    if (isNil(sourcePath)) return

    const { items: afterRemove, removed } = removeAtPath(items, sourcePath)
    if (isNil(removed)) return

    const adjustedPath = adjustPathAfterRemoval(targetPath, sourcePath)
    const newItems = insertAtPath(afterRemove, removed, adjustedPath, position)
    updateItems(newItems)
  }, [items, updateItems])

  const canDrop = useCallback((
    dragInfo: DragInfo,
    targetKey: string,
    position: DropPosition
  ): boolean => {
    const currentItems = itemsRef.current

    if (dragInfo.type === DragType.TREE_ITEM && dragInfo.data.key === targetKey) {
      return false
    }

    const targetPath = findItemPath(currentItems, targetKey)
    if (isNil(targetPath)) return false

    const targetData = getItemAtPath(currentItems, targetPath)
    if (isNil(targetData)) return false

    if (position === DropPosition.INTO) {
      return validateDropIntoTarget(dragInfo, targetData, targetPath, currentItems, operatorRegistry)
    }

    if (targetPath.length <= 1) {
      return true
    }

    const parentPath = targetPath.slice(0, -1)
    const parentData = getItemAtPath(currentItems, parentPath)

    return isNil(parentData) || validateDropToParent(dragInfo, parentData, targetPath, currentItems, operatorRegistry)
  }, [operatorRegistry])

  const canDropToRoot = useCallback((_dragInfo: DragInfo): boolean => {
    return true
  }, [])

  return {
    items,
    updateItems,
    updateItemAttributes,
    findPath,
    getItem,
    deleteByKey,
    insert,
    appendToRoot,
    move,
    canDrop,
    canDropToRoot
  }
}
