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
import { uuid, isNonEmptyString } from '@pimcore/studio-ui-bundle/utils'
import { isNil } from 'lodash'
import { type QueryEntityConfig, type ColumnConfig } from '../../types'

interface UseAvailableFieldsStateProps {
  entityConfig?: QueryEntityConfig
  onEntityConfigChange: (config: QueryEntityConfig) => void
}

export interface ChildItem {
  key?: string
  isOperator?: boolean
  attributes: Record<string, any>
}

export interface RemoveResult {
  removedItem: ChildItem | ColumnConfig | null
  newColumns: ColumnConfig[]
  parentKey?: string
  childIndex?: number
}

interface UseAvailableFieldsStateReturn {
  /** Columns with guaranteed UUIDs - ready to use for rendering */
  columns: ColumnConfig[]
  /** Update columns - handles syncing to parent automatically */
  updateColumns: (columns: ColumnConfig[]) => void
  /** Find a column or child by key */
  findColumnByKey: (key: string) => { column: ColumnConfig, columnIndex: number, childIndex?: number } | undefined
  /** Delete an item by key (column or child) */
  deleteItem: (key: string) => void
  /** Remove an item and return it along with new columns (for move operations - does NOT update state) */
  removeAndGetItem: (key: string, childIndex?: number, parentKey?: string) => RemoveResult
  /** Move an item to a new position at top level */
  moveToTopLevel: (item: ChildItem | ColumnConfig, insertIndex: number, workingColumns?: ColumnConfig[]) => void
  /** Move an item into an operator as a child */
  moveIntoOperator: (item: ChildItem, operatorKey: string, workingColumns?: ColumnConfig[]) => void
  /** Move an item as sibling to another child */
  moveAsSiblingOfChild: (item: ChildItem, parentKey: string, targetChildIndex: number, position: 'before' | 'after', sourceParentKey?: string, sourceChildIndex?: number, workingColumns?: ColumnConfig[]) => void
}

/**
 * Ensures all columns and children have UUID keys
 */
const enrichColumnsWithUUIDs = (columns: ColumnConfig[]): ColumnConfig[] => {
  return columns.map(column => {
    const enrichedColumn = {
      ...column,
      key: isNonEmptyString(column.key) ? column.key : uuid(),
      attributes: {
        ...column.attributes
      }
    }

    if (Array.isArray(column.attributes.children)) {
      enrichedColumn.attributes.children = column.attributes.children.map(child => ({
        ...child,
        key: isNonEmptyString(child.key) ? child.key : uuid(),
        attributes: child.attributes !== undefined ? { ...child.attributes } : undefined
      }))
    }

    return enrichedColumn
  })
}

export const useAvailableFieldsState = ({
  entityConfig,
  onEntityConfigChange
}: UseAvailableFieldsStateProps): UseAvailableFieldsStateReturn => {
  // Track whether we're the source of the update to avoid sync loops
  const isInternalUpdate = useRef(false)

  // Internal state - always has UUIDs
  const [columns, setColumns] = useState<ColumnConfig[]>(() => {
    const externalColumns = entityConfig?.columnConfig?.columns ?? []
    return enrichColumnsWithUUIDs(externalColumns)
  })

  // Sync from entityConfig when it changes externally
  useEffect(() => {
    // Skip if this was our own update
    if (isInternalUpdate.current) {
      isInternalUpdate.current = false
      return
    }

    const externalColumns = entityConfig?.columnConfig?.columns ?? []
    setColumns(enrichColumnsWithUUIDs(externalColumns))
  }, [entityConfig?.columnConfig?.columns])

  // Update columns - updates local state AND notifies parent
  const updateColumns = useCallback((newColumns: ColumnConfig[]): void => {
    if (isNil(entityConfig)) return

    // Mark as internal update to skip the sync effect
    isInternalUpdate.current = true

    // Update local state immediately (already has UUIDs from operations)
    setColumns(newColumns)

    // Notify parent with updated entity
    const updatedEntity: QueryEntityConfig = {
      ...entityConfig,
      columnConfig: {
        ...entityConfig.columnConfig,
        columns: newColumns
      }
    }

    onEntityConfigChange(updatedEntity)
  }, [entityConfig, onEntityConfigChange])

  // Find a column or child by key
  const findColumnByKey = useCallback((key: string): { column: ColumnConfig, columnIndex: number, childIndex?: number } | undefined => {
    const columnIndex = columns.findIndex(col => col.key === key)
    if (columnIndex !== -1) {
      return { column: columns[columnIndex], columnIndex }
    }

    // Search in children
    for (let i = 0; i < columns.length; i++) {
      const col = columns[i]
      if (Array.isArray(col.attributes.children)) {
        const childIdx = col.attributes.children.findIndex((child: any) => child.key === key)
        if (childIdx !== -1) {
          return { column: col, columnIndex: i, childIndex: childIdx }
        }
      }
    }

    return undefined
  }, [columns])

  // Delete an item by key (either top-level column or child)
  const deleteItem = useCallback((key: string): void => {
    const found = findColumnByKey(key)
    if (found === undefined) return

    const newColumns = [...columns]
    const { columnIndex, childIndex } = found

    if (childIndex !== undefined) {
      // Deleting a child
      const updatedColumn = {
        ...newColumns[columnIndex],
        attributes: {
          ...newColumns[columnIndex].attributes,
          children: [...(newColumns[columnIndex].attributes.children ?? [])]
        }
      }
      updatedColumn.attributes.children.splice(childIndex, 1)
      newColumns[columnIndex] = updatedColumn
    } else {
      // Deleting a top-level column
      newColumns.splice(columnIndex, 1)
    }

    updateColumns(newColumns)
  }, [columns, findColumnByKey, updateColumns])

  // Remove an item and return it (for move operations) - does NOT update state
  const removeAndGetItem = useCallback((key: string, childIndex?: number, parentKey?: string): RemoveResult => {
    const newColumns = [...columns]
    let removedItem: ChildItem | ColumnConfig | null = null

    if (childIndex !== undefined && !isNil(parentKey)) {
      // Removing a child node
      const parentIndex = newColumns.findIndex(col => col.key === parentKey)
      if (parentIndex !== -1) {
        const parent = { ...newColumns[parentIndex] }
        const children = Array.isArray(parent.attributes.children)
          ? [...parent.attributes.children]
          : []

        if (childIndex < children.length) {
          const [removed] = children.splice(childIndex, 1)
          removedItem = {
            ...removed,
            attributes: { ...removed.attributes }
          }
          parent.attributes = { ...parent.attributes, children }
          newColumns[parentIndex] = parent
        }
      }
    } else {
      // Removing a top-level column
      const columnIndex = newColumns.findIndex(col => col.key === key)
      if (columnIndex !== -1) {
        const [removed] = newColumns.splice(columnIndex, 1)
        removedItem = {
          ...removed,
          attributes: {
            ...removed.attributes,
            ...(Array.isArray(removed.attributes.children) ? {
              children: [...removed.attributes.children]
            } : {})
          }
        }
      }
    }

    // Return the modified columns - caller is responsible for updating state
    return { removedItem, newColumns, parentKey, childIndex }
  }, [columns])

  // Move an item to a new position at top level
  const moveToTopLevel = useCallback((item: ChildItem | ColumnConfig, insertIndex: number, workingColumns?: ColumnConfig[]): void => {
    const baseColumns = workingColumns ?? columns
    const newColumns = [...baseColumns]
    const columnItem: ColumnConfig = {
      ...item as ColumnConfig,
      // Only generate new UUID if item doesn't have a key (new items), preserve key for moves
      key: isNonEmptyString(item.key) ? item.key : uuid()
    }
    newColumns.splice(insertIndex, 0, columnItem)
    updateColumns(newColumns)
  }, [columns, updateColumns])

  // Move an item into an operator as a child
  const moveIntoOperator = useCallback((item: ChildItem, operatorKey: string, workingColumns?: ColumnConfig[]): void => {
    const baseColumns = workingColumns ?? columns
    const newColumns = [...baseColumns]

    const childItem: ChildItem = {
      ...item,
      // Only generate new UUID if item doesn't have a key (new items), preserve key for moves
      key: isNonEmptyString(item.key) ? item.key : uuid()
    }

    // Recursive function to find and add child to operator at any level
    const addChildToOperator = (children: ChildItem[], targetKey: string): boolean => {
      for (let i = 0; i < children.length; i++) {
        const child = children[i]
        
        if (child.key === targetKey) {
          // Found the target operator - add the child to it
          const childChildren = Array.isArray(child.attributes.children)
            ? [...child.attributes.children]
            : []
          childChildren.push(childItem)
          children[i] = {
            ...child,
            attributes: { ...child.attributes, children: childChildren }
          }
          return true
        }

        // Recursively search in this child's children
        if (Array.isArray(child.attributes.children) && child.attributes.children.length > 0) {
          const childrenCopy = [...child.attributes.children]
          if (addChildToOperator(childrenCopy, targetKey)) {
            children[i] = {
              ...child,
              attributes: { ...child.attributes, children: childrenCopy }
            }
            return true
          }
        }
      }
      return false
    }

    // First check if it's a top-level operator
    const operatorIndex = newColumns.findIndex(col => col.key === operatorKey)
    if (operatorIndex !== -1) {
      const operator = { ...newColumns[operatorIndex] }
      const children = Array.isArray(operator.attributes.children)
        ? [...operator.attributes.children]
        : []
      children.push(childItem)
      operator.attributes = { ...operator.attributes, children }
      newColumns[operatorIndex] = operator
      updateColumns(newColumns)
      return
    }

    // If not found at top level, search recursively in all columns
    for (let i = 0; i < newColumns.length; i++) {
      const column = newColumns[i]
      if (Array.isArray(column.attributes.children) && column.attributes.children.length > 0) {
        const childrenCopy = [...column.attributes.children]
        if (addChildToOperator(childrenCopy, operatorKey)) {
          newColumns[i] = {
            ...column,
            attributes: { ...column.attributes, children: childrenCopy }
          }
          updateColumns(newColumns)
          return
        }
      }
    }
  }, [columns, updateColumns])

  // Move an item as sibling to another child
  const moveAsSiblingOfChild = useCallback((
    item: ChildItem,
    parentKey: string,
    targetChildIndex: number,
    position: 'before' | 'after',
    sourceParentKey?: string,
    sourceChildIndex?: number,
    workingColumns?: ColumnConfig[]
  ): void => {
    const baseColumns = workingColumns ?? columns
    const parentIndex = baseColumns.findIndex(col => col.key === parentKey)
    if (parentIndex === -1) return

    const newColumns = [...baseColumns]
    const parent = { ...newColumns[parentIndex] }
    const children = Array.isArray(parent.attributes.children)
      ? [...parent.attributes.children]
      : []

    // Calculate insert position, adjusting for prior removal if from same parent
    let insertIndex = position === 'before' ? targetChildIndex : targetChildIndex + 1

    if (sourceParentKey === parentKey && sourceChildIndex !== undefined && sourceChildIndex < targetChildIndex) {
      // Source was before target in same parent, adjust index
      insertIndex = position === 'before' ? targetChildIndex - 1 : targetChildIndex
    }

    const childItem: ChildItem = {
      ...item,
      // Only generate new UUID if item doesn't have a key (new items), preserve key for moves
      key: isNonEmptyString(item.key) ? item.key : uuid()
    }
    children.splice(insertIndex, 0, childItem)

    parent.attributes = { ...parent.attributes, children }
    newColumns[parentIndex] = parent
    updateColumns(newColumns)
  }, [columns, updateColumns])

  return {
    columns,
    updateColumns,
    findColumnByKey,
    deleteItem,
    removeAndGetItem,
    moveToTopLevel,
    moveIntoOperator,
    moveAsSiblingOfChild
  }
}