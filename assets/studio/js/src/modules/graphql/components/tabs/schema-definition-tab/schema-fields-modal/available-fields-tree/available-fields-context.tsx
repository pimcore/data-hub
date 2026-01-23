/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { createContext, useContext, useMemo, useCallback } from 'react'
import { useInjection, serviceIds } from '@pimcore/studio-ui-bundle/app'
import { isNil } from 'lodash'
import { type TreeNode, type QueryEntityConfig, type ColumnConfig } from '../types'
import { type DynamicTypeOperatorRegistry } from '../../../../../../operators/dynamic-type-operator-registry'
import { type DynamicTypeFieldDefinitionRegistry } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { useAvailableFieldsState } from './hooks/use-available-fields-state'
import { useDropValidation } from './hooks/use-drop-validation'

interface AvailableFieldsContextValue {
  // State
  columns: ColumnConfig[]
  treeData: TreeNode[]
  
  // Registries
  operatorRegistry: DynamicTypeOperatorRegistry
  fieldDefinitionRegistry: DynamicTypeFieldDefinitionRegistry
  
  // Actions
  updateColumns: (columns: ColumnConfig[]) => void
  findColumnByKey: (key: string) => { column: ColumnConfig, columnIndex: number, childIndex?: number } | undefined
  deleteItem: (key: string) => void
  
  // Validation
  isValidContext: (info: any, targetNodeKey: string) => boolean
  isValidDropIntoOperator: (targetNode: TreeNode) => boolean
  isValidSiblingDrop: (targetNode: TreeNode) => boolean
  
  // Drop handling
  onDrop: (info: any, targetKey: string, position: 'before' | 'after', isIntoOperator: boolean) => void
}

const AvailableFieldsContext = createContext<AvailableFieldsContextValue | null>(null)

export const useAvailableFieldsContext = (): AvailableFieldsContextValue => {
  const context = useContext(AvailableFieldsContext)
  if (context === null) {
    throw new Error('useAvailableFieldsContext must be used within an AvailableFieldsProvider')
  }
  return context
}

interface AvailableFieldsProviderProps {
  children: React.ReactNode
  entityConfig?: QueryEntityConfig
  operatorRegistryServiceId: string
  onEntityConfigChange: (config: QueryEntityConfig) => void
  buildTreeFromColumns: (columns: ColumnConfig[], operatorRegistry: DynamicTypeOperatorRegistry, fieldDefinitionRegistry: DynamicTypeFieldDefinitionRegistry) => TreeNode[]
}

export const AvailableFieldsProvider = ({
  children,
  entityConfig,
  operatorRegistryServiceId,
  onEntityConfigChange,
  buildTreeFromColumns
}: AvailableFieldsProviderProps): React.JSX.Element => {
  // Registries
  const operatorRegistry = useInjection<DynamicTypeOperatorRegistry>(operatorRegistryServiceId)
  const fieldDefinitionRegistry = useInjection<DynamicTypeFieldDefinitionRegistry>(serviceIds['DynamicTypes/FieldDefinitionRegistry'])

  // State management
  const {
    columns,
    updateColumns,
    findColumnByKey,
    deleteItem,
    removeAndGetItem,
    moveToTopLevel,
    moveIntoOperator,
    moveAsSiblingOfChild
  } = useAvailableFieldsState({ entityConfig, onEntityConfigChange })

  // Validation
  const {
    isValidContext,
    isValidDropIntoOperator,
    isValidSiblingDrop
  } = useDropValidation({ columns, operatorRegistry })

  // Build tree data
  const treeData = useMemo(
    () => buildTreeFromColumns(columns, operatorRegistry, fieldDefinitionRegistry),
    [columns, operatorRegistry, fieldDefinitionRegistry, buildTreeFromColumns]
  )

  // Helper to create item from drag data
  const createItemFromDragData = useCallback((dragType: string, dragData: any, capturedItem?: any): any => {
    if (dragType === 'class-attribute') {
      return {
        isOperator: false,
        attributes: {
          attribute: String(dragData.key),
          label: String(dragData.title),
          dataType: String(dragData.dataType ?? 'text')
        }
      }
    } else if (dragType === 'operator') {
      return {
        isOperator: true,
        attributes: {
          label: String(dragData.title),
          class: String(dragData.operatorId),
          dataType: 'operator',
          children: []
        }
      }
    } else if (dragType === 'available-field' && !isNil(capturedItem)) {
      return capturedItem
    }
    return null
  }, [])

  // Drop handler
  const onDrop = useCallback((info: any, targetKey?: string, position: 'before' | 'after' = 'after', isIntoOperator: boolean = false): void => {
    const { type, data } = info

    // Find target node
    const findNode = (nodes: TreeNode[], key: string): TreeNode | undefined => {
      for (const node of nodes) {
        if (node.key === key) return node
        if (!isNil(node.children)) {
          const found = findNode(node.children, key)
          if (!isNil(found)) return found
        }
      }
      return undefined
    }

    const targetNode = !isNil(targetKey) ? findNode(treeData, targetKey) : undefined

    // Defer state update to allow drag end event to complete
    setTimeout(() => {
      try {
        // For move operations, first remove the source item
        let capturedItem: any = null
        let workingColumns: ColumnConfig[] = columns
        
        if (type === 'available-field') {
          const { removedItem, newColumns } = removeAndGetItem(
            data.sourceKey,
            data.sourceChildIndex,
            data.sourceParentKey
          )
          capturedItem = removedItem
          workingColumns = newColumns
          if (isNil(capturedItem)) return
        }

        // Create the new item
        const newItem = createItemFromDragData(type, data, capturedItem)
        if (isNil(newItem)) return

        // Case 1: Dropping into an operator as a child
        if (isIntoOperator && !isNil(targetNode) && targetNode.isOperator === true) {
          moveIntoOperator(newItem, String(targetNode.key), workingColumns)
          return
        }

        // Case 2: Dropping as sibling to a child node
        if (!isNil(targetNode) && targetNode.childIndex !== undefined && !isNil(targetNode.columnConfig)) {
          moveAsSiblingOfChild(
            newItem,
            String(targetNode.columnConfig.key),
            targetNode.childIndex,
            position,
            data.sourceParentKey,
            data.sourceChildIndex,
            workingColumns
          )
          return
        }

        // Case 3: Dropping at top level
        let insertIndex = workingColumns.length
        if (targetKey !== undefined) {
          const targetIndex = workingColumns.findIndex(col => col.key === targetKey)
          if (targetIndex !== -1) {
            insertIndex = position === 'before' ? targetIndex : targetIndex + 1
          }
        }

        moveToTopLevel(newItem, insertIndex, workingColumns)
      } catch (error) {
        console.error('Error in onDrop handler:', error)
      }
    }, 0)
  }, [columns, treeData, createItemFromDragData, removeAndGetItem, moveIntoOperator, moveAsSiblingOfChild, moveToTopLevel])

  const contextValue = useMemo<AvailableFieldsContextValue>(() => ({
    columns,
    treeData,
    operatorRegistry,
    fieldDefinitionRegistry,
    updateColumns,
    findColumnByKey,
    deleteItem,
    isValidContext,
    isValidDropIntoOperator,
    isValidSiblingDrop,
    onDrop
  }), [
    columns,
    treeData,
    operatorRegistry,
    fieldDefinitionRegistry,
    updateColumns,
    findColumnByKey,
    deleteItem,
    isValidContext,
    isValidDropIntoOperator,
    isValidSiblingDrop,
    onDrop
  ])

  return (
    <AvailableFieldsContext.Provider value={ contextValue }>
      {children}
    </AvailableFieldsContext.Provider>
  )
}
