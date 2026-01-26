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
import { uuid } from '@pimcore/studio-ui-bundle/utils'
import { type DynamicTypeOperatorRegistry } from '../../../../../../../operators/dynamic-type-operator-registry'
import { type DynamicTypeFieldDefinitionRegistry } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { type QueryEntityConfig } from '../../types'
import { useTreeState, type DragInfo } from './use-tree-state'
import { DragType, DropPosition } from '../../drag-types'
import {
  type TreeItemData,
  type TreePath
} from '../tree-item'

export { type DragInfo } from './use-tree-state'

interface TreeContextValue {
  items: TreeItemData[]
  operatorRegistry: DynamicTypeOperatorRegistry
  fieldDefinitionRegistry: DynamicTypeFieldDefinitionRegistry
  findPath: (key: string) => TreePath | null
  getItem: (path: TreePath) => TreeItemData | null
  deleteByKey: (key: string) => void
  insert: (item: TreeItemData, targetPath: TreePath, position: DropPosition) => void
  move: (sourceKey: string, targetPath: TreePath, position: DropPosition) => void
  updateItemAttributes: (key: string, attributes: Record<string, any>) => void
  canDrop: (dragInfo: DragInfo, targetKey: string, position: DropPosition) => boolean
  canDropToRoot: (dragInfo: DragInfo) => boolean
  isValidDragType: (dragInfo: DragInfo) => boolean
  handleDrop: (dragInfo: DragInfo, targetKey: string, position: DropPosition) => void
  handleDropToRoot: (dragInfo: DragInfo) => void
}

const TreeContext = createContext<TreeContextValue | null>(null)

export const useTreeContext = (): TreeContextValue => {
  const context = useContext(TreeContext)
  if (context === null) {
    throw new Error('useTreeContext must be used within a TreeProvider')
  }
  return context
}

interface TreeProviderProps {
  children: React.ReactNode
  entityConfig?: QueryEntityConfig
  operatorRegistryServiceId: string
  onEntityConfigChange: (config: QueryEntityConfig) => void
  onOperatorAdded?: (item: TreeItemData) => void
}

export const TreeProvider = ({
  children,
  entityConfig,
  operatorRegistryServiceId,
  onEntityConfigChange,
  onOperatorAdded
}: TreeProviderProps): React.JSX.Element => {
  const operatorRegistry = useInjection<DynamicTypeOperatorRegistry>(operatorRegistryServiceId)
  const fieldDefinitionRegistry = useInjection<DynamicTypeFieldDefinitionRegistry>(
    serviceIds['DynamicTypes/FieldDefinitionRegistry']
  )

  const {
    items,
    findPath,
    getItem,
    deleteByKey,
    insert,
    appendToRoot,
    updateItemAttributes,
    move,
    canDrop,
    canDropToRoot
  } = useTreeState({
    entityConfig,
    onEntityConfigChange,
    operatorRegistry
  })

  const isValidDragType = useCallback((dragInfo: DragInfo): boolean => {
    return dragInfo.type === DragType.CLASS_ATTRIBUTE ||
           dragInfo.type === DragType.OPERATOR ||
           dragInfo.type === DragType.TREE_ITEM
  }, [])

  const handleDrop = useCallback((
    dragInfo: DragInfo,
    targetKey: string,
    position: DropPosition
  ): void => {
    const targetPath = findPath(targetKey)
    if (targetPath === null) {
      console.warn('handleDrop: Target not found', targetKey)
      return
    }

    if (dragInfo.type === DragType.TREE_ITEM && dragInfo.data.key !== undefined) {
      move(dragInfo.data.key, targetPath, position)
      return
    }

    let newItem: TreeItemData | null = null

    if (dragInfo.type === 'class-attribute' && dragInfo.data.dataType !== undefined) {
      const fieldDefinition = fieldDefinitionRegistry.getDynamicType(dragInfo.data.dataType, false)
      if (fieldDefinition === undefined) {
        console.warn('Field definition not found for', dragInfo.data.dataType)
        return
      }
      newItem = {
        key: uuid(),
        isOperator: false,
        attributes: {
          attribute: String(dragInfo.data.key ?? ''),
          label: String(dragInfo.data.title ?? ''),
          dataType: String(dragInfo.data.dataType ?? 'text')
        }
      }
    } else if (dragInfo.type === DragType.OPERATOR && dragInfo.data.operatorId !== undefined) {
      const operator = operatorRegistry.getDynamicType(dragInfo.data.operatorId, false)
      if (operator === undefined) {
        console.warn('Operator not found for', dragInfo.data.operatorId)
        return
      }
      newItem = {
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

    if (newItem !== null) {
      insert(newItem, targetPath, position)
      if (newItem.isOperator) {
        onOperatorAdded?.(newItem)
      }
    }
  }, [findPath, move, insert, fieldDefinitionRegistry, operatorRegistry, onOperatorAdded])

  const handleDropToRoot = useCallback((dragInfo: DragInfo): void => {
    if (dragInfo.type === DragType.TREE_ITEM && dragInfo.data.key !== undefined) {
      const sourcePath = findPath(dragInfo.data.key)
      if (sourcePath !== null && sourcePath.length === 1) {
        return
      }
      move(dragInfo.data.key, [items.length], DropPosition.BEFORE)
      return
    }

    let newItem: TreeItemData | null = null

    if (dragInfo.type === DragType.CLASS_ATTRIBUTE && dragInfo.data.dataType !== undefined) {
      const fieldDefinition = fieldDefinitionRegistry.getDynamicType(dragInfo.data.dataType, false)
      if (fieldDefinition === undefined) {
        return
      }
      newItem = {
        key: uuid(),
        isOperator: false,
        attributes: {
          attribute: String(dragInfo.data.key ?? ''),
          label: String(dragInfo.data.title ?? ''),
          dataType: String(dragInfo.data.dataType ?? 'text')
        }
      }
    } else if (dragInfo.type === DragType.OPERATOR && dragInfo.data.operatorId !== undefined) {
      const operator = operatorRegistry.getDynamicType(dragInfo.data.operatorId, false)
      if (operator === undefined) {
        return
      }
      newItem = {
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

    if (newItem !== null) {
      appendToRoot(newItem)
      if (newItem.isOperator) {
        onOperatorAdded?.(newItem)
      }
    }
  }, [items, findPath, move, appendToRoot, fieldDefinitionRegistry, operatorRegistry, onOperatorAdded])

  const value = useMemo((): TreeContextValue => ({
    items,
    operatorRegistry,
    fieldDefinitionRegistry,
    findPath,
    getItem,
    deleteByKey,
    insert,
    move,
    updateItemAttributes,
    canDrop,
    canDropToRoot,
    isValidDragType,
    handleDrop,
    handleDropToRoot
  }), [
    items,
    operatorRegistry,
    fieldDefinitionRegistry,
    findPath,
    getItem,
    deleteByKey,
    insert,
    move,
    updateItemAttributes,
    canDrop,
    canDropToRoot,
    isValidDragType,
    handleDrop,
    handleDropToRoot
  ])

  return (
    <TreeContext.Provider value={ value }>
      {children}
    </TreeContext.Provider>
  )
}
