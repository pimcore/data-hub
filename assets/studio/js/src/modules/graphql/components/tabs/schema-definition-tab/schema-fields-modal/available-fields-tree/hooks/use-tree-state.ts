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
import { uuid } from '@pimcore/studio-ui-bundle/utils'
import { type DragAndDropInfo } from '@pimcore/studio-ui-bundle/components'
import { type QueryEntityConfig, type ColumnConfig } from '../../types'
import {
  type TreeItemData,
  type TreePath,
  ensureKeys,
  findItemPath,
  getItemAtPath,
  insertAtPath,
  removeAtPath,
  createTreeItem
} from '../tree-item'
import { type DynamicTypeOperatorRegistry } from '../../../../../../../operators/dynamic-type-operator-registry'
import { DragType, DropPosition } from '../../drag-types'

interface UseTreeStateProps {
  entityConfig?: QueryEntityConfig
  onEntityConfigChange: (config: QueryEntityConfig) => void
  operatorRegistry: DynamicTypeOperatorRegistry
}

interface UseTreeStateReturn {
  items: TreeItemData[]
  updateItems: (items: TreeItemData[]) => void
  updateItemAttributes: (key: string, attributes: Record<string, any>) => void
  findPath: (key: string) => TreePath | null
  getItem: (path: TreePath) => TreeItemData | null
  deleteByKey: (key: string) => void
  insert: (item: TreeItemData, targetPath: TreePath, position: DropPosition) => void
  appendToRoot: (item: TreeItemData) => void
  move: (sourceKey: string, targetPath: TreePath, position: DropPosition) => void
  canDrop: (dragInfo: DragInfo, targetKey: string, position: DropPosition) => boolean
  canDropToRoot: (dragInfo: DragInfo) => boolean
}

export interface DragInfo extends DragAndDropInfo {
  type: DragType.CLASS_ATTRIBUTE | DragType.OPERATOR | DragType.TREE_ITEM
  data: {
    key?: string
    title?: string
    dataType?: string
    operatorId?: string
    [key: string]: any
  }
}

function columnsToItems (columns: ColumnConfig[]): TreeItemData[] {
  const convertChildren = (children: Array<Record<string, any>>): TreeItemData[] => {
    return children.map((child: Record<string, any>) => ({
      key: String(child.key ?? ''),
      isOperator: child.isOperator === true,
      attributes: {
        ...child.attributes,
        ...(Array.isArray(child.attributes?.children)
          ? { children: convertChildren(child.attributes.children as Array<Record<string, any>>) }
          : {})
      }
    }))
  }

  return columns.map((col: ColumnConfig) => ({
    key: String(col.key ?? ''),
    isOperator: col.isOperator,
    attributes: {
      ...col.attributes,
      ...(Array.isArray(col.attributes?.children)
        ? { children: convertChildren(col.attributes.children) }
        : {})
    }
  })) as TreeItemData[]
}

function itemsToColumns (items: TreeItemData[]): ColumnConfig[] {
  const convertChildren = (children: TreeItemData[]): any[] => {
    return children.map(child => ({
      key: child.key,
      isOperator: child.isOperator,
      attributes: {
        ...child.attributes,
        ...(Array.isArray(child.attributes.children)
          ? { children: convertChildren(child.attributes.children) }
          : {})
      }
    }))
  }

  return items.map(item => ({
    key: item.key,
    isOperator: item.isOperator,
    attributes: {
      ...item.attributes,
      ...(Array.isArray(item.attributes.children)
        ? { children: convertChildren(item.attributes.children) }
        : {})
    }
  })) as ColumnConfig[]
}

function createItemFromDragInfo (dragInfo: DragInfo): TreeItemData | null {
  if (dragInfo.type === DragType.CLASS_ATTRIBUTE) {
    return {
      key: uuid(),
      isOperator: false,
      attributes: {
        attribute: String(dragInfo.data.key ?? ''),
        label: String(dragInfo.data.title ?? ''),
        dataType: String(dragInfo.data.dataType ?? 'text')
      }
    }
  }

  if (dragInfo.type === DragType.OPERATOR) {
    return {
      key: uuid(),
      isOperator: true,
      attributes: {
        label: String(dragInfo.data.title ?? ''),
        class: String(dragInfo.data.operatorId ?? ''),
        type: DragType.OPERATOR,
        children: []
      }
    }
  }

  return null
}

export const useTreeState = ({
  entityConfig,
  onEntityConfigChange,
  operatorRegistry
}: UseTreeStateProps): UseTreeStateReturn => {
  const isInternalUpdate = useRef(false)

  const [items, setItems] = useState<TreeItemData[]>(() => {
    const externalColumns = entityConfig?.columnConfig?.columns ?? []
    return ensureKeys(columnsToItems(externalColumns))
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
    setItems(ensureKeys(columnsToItems(externalColumns)))
  }, [entityConfig])

  const updateItems = useCallback((newItems: TreeItemData[]): void => {
    isInternalUpdate.current = true
    itemsRef.current = newItems
    setItems(newItems)

    const columns = itemsToColumns(newItems)
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

  const getItem = useCallback((path: TreePath): TreeItemData | null => {
    return getItemAtPath(items, path)
  }, [items])

  const deleteByKey = useCallback((key: string): void => {
    const path = findItemPath(items, key)
    if (path === null) return

    const { items: newItems } = removeAtPath(items, path)
    updateItems(newItems)
  }, [items, updateItems])

  const insert = useCallback((
    item: TreeItemData,
    targetPath: TreePath,
    position: DropPosition
  ): void => {
    const newItems = insertAtPath(items, item, targetPath, position)
    updateItems(newItems)
  }, [items, updateItems])

  const appendToRoot = useCallback((item: TreeItemData): void => {
    const newItems = [...items, item]
    updateItems(newItems)
  }, [items, updateItems])

  const updateItemAttributes = useCallback((key: string, attributes: Record<string, any>): void => {
    const updateRecursive = (itemList: TreeItemData[]): TreeItemData[] => {
      return itemList.map(item => {
        if (item.key === key) {
          return {
            ...item,
            attributes: {
              ...item.attributes,
              ...attributes
            }
          }
        }
        if (Array.isArray(item.attributes.children)) {
          return {
            ...item,
            attributes: {
              ...item.attributes,
              children: updateRecursive(item.attributes.children)
            }
          }
        }
        return item
      })
    }

    const newItems = updateRecursive(items)
    updateItems(newItems)
  }, [items, updateItems])

  const move = useCallback((
    sourceKey: string,
    targetPath: TreePath,
    position: DropPosition
  ): void => {
    const sourcePath = findItemPath(items, sourceKey)
    if (sourcePath === null) return

    const { items: afterRemove, removed } = removeAtPath(items, sourcePath)
    if (removed === null) return

    const adjustedPath = [...targetPath]
    if (sourcePath.length === targetPath.length) {
      const sameParent = sourcePath.slice(0, -1).every((v, i) => v === targetPath[i])
      if (sameParent && sourcePath[sourcePath.length - 1] < targetPath[targetPath.length - 1]) {
        adjustedPath[adjustedPath.length - 1]--
      }
    }

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
    if (targetPath === null) return false

    const targetData = getItemAtPath(currentItems, targetPath)
    if (targetData === null) return false

    if (position === DropPosition.INTO) {
      const targetItem = createTreeItem(targetData, operatorRegistry)
      if (!targetItem.canHaveChildren()) return false

      if (dragInfo.type === DragType.TREE_ITEM && dragInfo.data.key !== undefined) {
        const sourcePath = findItemPath(currentItems, dragInfo.data.key)
        if (sourcePath !== null) {
          const sourceData = getItemAtPath(currentItems, sourcePath)
          if (sourceData !== null) {
            const sourceItem = createTreeItem(sourceData, operatorRegistry)

            const isAlreadyChild = sourcePath.length === targetPath.length + 1 &&
              sourcePath.slice(0, -1).every((v, i) => v === targetPath[i])

            return targetItem.canAcceptChild(sourceItem, isAlreadyChild)
          }
        }
      }

      const newItemData = createItemFromDragInfo(dragInfo)
      if (newItemData !== null) {
        const newItem = createTreeItem(newItemData, operatorRegistry)
        return targetItem.canAcceptChild(newItem, false)
      }
    }

    if (targetPath.length > 1 && position !== DropPosition.INTO) {
      const parentPath = targetPath.slice(0, -1)
      const parentData = getItemAtPath(currentItems, parentPath)
      if (parentData !== null) {
        const parentItem = createTreeItem(parentData, operatorRegistry)

        if (dragInfo.type === DragType.TREE_ITEM && dragInfo.data.key !== undefined) {
          const sourcePath = findItemPath(currentItems, dragInfo.data.key)
          if (sourcePath === null) {
            return false
          }

          if (sourcePath.length === targetPath.length) {
            const sameParent = sourcePath.slice(0, -1).every((v, i) => v === parentPath[i])
            if (sameParent) return true
          }

          const sourceData = getItemAtPath(currentItems, sourcePath)
          if (sourceData !== null) {
            const sourceItem = createTreeItem(sourceData, operatorRegistry)
            return parentItem.canAcceptChild(sourceItem, false)
          }
          return false
        }

        const newItemData = createItemFromDragInfo(dragInfo)
        if (newItemData !== null) {
          const newItem = createTreeItem(newItemData, operatorRegistry)
          return parentItem.canAcceptChild(newItem, false)
        }
      }
    }

    return true
  }, [operatorRegistry])

  const canDropToRoot = useCallback((dragInfo: DragInfo): boolean => {
    return dragInfo.type === DragType.CLASS_ATTRIBUTE ||
           dragInfo.type === DragType.OPERATOR ||
           dragInfo.type === DragType.TREE_ITEM
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
