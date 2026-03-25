/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useCallback } from 'react'
import { get, isNil } from 'lodash'
import { type TreeNode, type PersistedColumnConfig } from '../../types'
import { type DynamicTypeOperatorRegistry } from '../../../../../../../operators/dynamic-type-operator-registry'
import { createSourceConfigFromDragInfo } from '../source-config-utils'
import { DragType, type DragInfo } from '../../drag-types'

interface UseDropValidationProps {
  columns: PersistedColumnConfig[]
  operatorRegistry: DynamicTypeOperatorRegistry
}

interface UseDropValidationReturn {
  isValidContext: (info: DragInfo, targetNodeKey: string) => boolean
  isValidDropIntoOperator: (targetNode: TreeNode, dragInfo?: DragInfo) => boolean
  isValidSiblingDrop: (targetNode: TreeNode, dragInfo?: DragInfo) => boolean
}

export const useDropValidation = ({
  columns,
  operatorRegistry
}: UseDropValidationProps): UseDropValidationReturn => {
  const isValidContext = useCallback((info: DragInfo, targetNodeKey: string): boolean => {
    if (info.type === DragType.TREE_ITEM && info.data?.key === targetNodeKey) {
      return false
    }
    return true
  }, [])

  const getNodeConfig = useCallback((node: TreeNode): any => {
    if (node.childIndex === undefined) {
      const column = columns.find(col => col.key === node.columnConfig?.key)
      return column
    }

    const parentColumn = columns.find(col => col.key === node.columnConfig?.key)
    if (isNil(parentColumn) || !Array.isArray(parentColumn.attributes?.children)) {
      return null
    }

    const findChildByKey = (children: any[], targetKey: string): any => {
      for (const child of children) {
        if (child.key === targetKey) {
          return child
        }
        if (Array.isArray(child.attributes?.children)) {
          const found = findChildByKey(child.attributes.children as any[], targetKey)
          if (!isNil(found)) return found
        }
      }
      return null
    }

    return findChildByKey(parentColumn.attributes.children, String(node.key))
  }, [columns])

  const isValidDropIntoOperator = useCallback((targetNode: TreeNode, dragInfo?: DragInfo): boolean => {
    if (targetNode.isOperator !== true) {
      return false
    }

    const operatorClass = isNil(targetNode.childIndex)
      ? get(targetNode, 'columnConfig.attributes.class')
      : get(
          targetNode,
          ['columnConfig', 'attributes', 'children', targetNode.childIndex, 'attributes', 'class']
        )

    const operatorType = operatorRegistry.getDynamicType(
      String(operatorClass ?? ''),
      false
    )

    if (isNil(operatorType)) {
      return false
    }

    const allowsChildrenAtAll = operatorType.allowsChildren?.() ?? false
    if (!allowsChildrenAtAll) {
      return false
    }

    const currentConfig = getNodeConfig(targetNode)

    if (!isNil(currentConfig)) {
      const sourceConfig = createSourceConfigFromDragInfo(dragInfo)

      const canAcceptChild = operatorType.allowChild?.(currentConfig as PersistedColumnConfig, sourceConfig) ?? true
      if (!canAcceptChild) {
        return false
      }
    }

    return true
  }, [operatorRegistry, getNodeConfig])

  const isValidSiblingDrop = useCallback((targetNode: TreeNode, dragInfo?: DragInfo): boolean => {
    if (targetNode.childIndex !== undefined && !isNil(targetNode.columnConfig)) {
      const parentColumn = columns.find(col => col.key === targetNode.columnConfig?.key)

      if (!isNil(parentColumn) && !isNil(parentColumn.attributes.class)) {
        const operatorType = operatorRegistry.getDynamicType(parentColumn.attributes.class, false)

        if (isNil(operatorType)) {
          return false
        }

        if (!(operatorType.allowsChildren?.())) {
          return false
        }

        if (dragInfo?.type === DragType.TREE_ITEM && dragInfo.data?.parentKey === parentColumn.key) {
          return true
        }

        const sourceConfig = createSourceConfigFromDragInfo(dragInfo)

        const canAcceptChild = operatorType.allowChild?.(parentColumn, sourceConfig) ?? true
        return canAcceptChild
      }
    }

    return true
  }, [columns, operatorRegistry])

  return {
    isValidContext,
    isValidDropIntoOperator,
    isValidSiblingDrop
  }
}
