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
import { isNil } from 'lodash'
import { useInjection, serviceIds } from '@pimcore/studio-ui-bundle/app'
import { type DynamicTypeOperatorRegistry } from '../../../../../../../operators/dynamic-type-operator-registry'
import { type DynamicTypeFieldDefinitionRegistry } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { type QueryEntityConfig } from '../../types'
import { useTreeState } from './use-tree-state'
import { DragType, DropPosition, type DragInfo } from '../../drag-types'
import { type InternalTreeNode, type TreePath } from '../tree-item/tree-item'
import { createItemFromDragInfo } from '../utils/tree-conversion-utils'

export { type DragInfo } from '../../drag-types'

interface TreeContextValue {
  items: InternalTreeNode[]
  operatorRegistry: DynamicTypeOperatorRegistry
  fieldDefinitionRegistry: DynamicTypeFieldDefinitionRegistry
  findPath: (key: string) => TreePath | null
  getItem: (path: TreePath) => InternalTreeNode | null
  deleteByKey: (key: string) => void
  insert: (item: InternalTreeNode, targetPath: TreePath, position: DropPosition) => void
  move: (sourceKey: string, targetPath: TreePath, position: DropPosition) => void
  updateItemAttributes: (key: string, attributes: Record<string, any>) => void
  canDrop: (dragInfo: DragInfo, targetKey: string, position: DropPosition) => boolean
  canDropToRoot: (dragInfo: DragInfo) => boolean
  isValidDragType: (dragInfo: DragInfo) => boolean
  handleDrop: (dragInfo: DragInfo, targetKey: string, position: DropPosition) => void
  handleDropToRoot: (dragInfo: DragInfo) => void
  onItemInserted?: (targetKey: string, position: DropPosition) => void
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
  disabled?: boolean
  onEntityConfigChange: (config: QueryEntityConfig) => void
  onOperatorAdded?: (item: InternalTreeNode) => void
}

export const TreeProvider = ({
  children,
  entityConfig,
  operatorRegistryServiceId,
  disabled = false,
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
    if (isNil(targetPath)) {
      console.warn('handleDrop: Target not found', targetKey)
      return
    }

    if (dragInfo.type === DragType.TREE_ITEM && !isNil(dragInfo.data.key)) {
      move(String(dragInfo.data.key), targetPath, position)
      return
    }

    const newItem = createItemFromDragInfo(dragInfo)
    if (!isNil(newItem)) {
      insert(newItem, targetPath, position)
      if (newItem.isOperator) {
        onOperatorAdded?.(newItem)
      }
    }
  }, [findPath, move, insert, onOperatorAdded])

  const handleDropToRoot = useCallback((dragInfo: DragInfo): void => {
    if (dragInfo.type === DragType.TREE_ITEM && !isNil(dragInfo.data.key)) {
      const sourcePath = findPath(String(dragInfo.data.key))
      if (!isNil(sourcePath) && sourcePath.length === 1) {
        return
      }
      move(String(dragInfo.data.key), [items.length], DropPosition.BEFORE)
      return
    }

    const newItem = createItemFromDragInfo(dragInfo)
    if (!isNil(newItem)) {
      appendToRoot(newItem)
      if (newItem.isOperator) {
        onOperatorAdded?.(newItem)
      }
    }
  }, [items, findPath, move, appendToRoot, onOperatorAdded])

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
