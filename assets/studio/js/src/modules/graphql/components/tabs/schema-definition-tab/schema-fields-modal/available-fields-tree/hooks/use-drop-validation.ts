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
import { isNil } from 'lodash'
import { type TreeNode, type ColumnConfig } from '../../types'
import { type DynamicTypeOperatorRegistry } from '../../../../../../../operators/dynamic-type-operator-registry'
import { createSourceConfigFromDragInfo } from '../source-config-utils'
import { DragType } from '../../drag-types'

interface UseDropValidationProps {
  columns: ColumnConfig[]
  operatorRegistry: DynamicTypeOperatorRegistry
}

interface DropInfo {
  type: string
  data?: {
    sourceKey?: string
    [key: string]: any
  }
}

interface UseDropValidationReturn {
  /** Check if the drag type is valid for this tree */
  isValidContext: (info: DropInfo, targetNodeKey: string) => boolean
  /** Check if an operator can accept a child */
  isValidDropIntoOperator: (targetNode: TreeNode, dragInfo?: DropInfo) => boolean
  /** Check if a sibling drop is valid (for child nodes, checks parent constraints) */
  isValidSiblingDrop: (targetNode: TreeNode, dragInfo?: DropInfo) => boolean
}

export const useDropValidation = ({
  columns,
  operatorRegistry
}: UseDropValidationProps): UseDropValidationReturn => {
  const isValidContext = useCallback((info: DropInfo, targetNodeKey: string): boolean => {
    const dropType = info.type

    if (dropType !== DragType.CLASS_ATTRIBUTE && dropType !== DragType.OPERATOR && dropType !== DragType.TREE_ITEM) {
      return false
    }

    if (dropType === DragType.TREE_ITEM && info.data?.sourceKey === targetNodeKey) {
      return false
    }

    return true
  }, [])

  // Handles nested operators by traversing the tree
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

  const isValidDropIntoOperator = useCallback((targetNode: TreeNode, dragInfo?: DropInfo): boolean => {
    if (targetNode.isOperator !== true) {
      return false
    }

    const operatorClass = targetNode.childIndex !== undefined
      ? (targetNode.columnConfig?.attributes?.children?.[targetNode.childIndex]?.attributes as any)?.class
      : (targetNode.columnConfig?.attributes as any)?.class

    const operatorType = operatorRegistry.getDynamicType(
      String(operatorClass ?? ''),
      false
    )

    if (operatorType === undefined) {
      return false
    }

    const allowsChildrenAtAll = operatorType.allowsChildren?.() ?? false
    if (!allowsChildrenAtAll) {
      return false
    }

    const currentConfig = getNodeConfig(targetNode)

    if (!isNil(currentConfig)) {
      const sourceConfig = createSourceConfigFromDragInfo(dragInfo)

      const canAcceptChild = operatorType.allowChild?.(currentConfig as ColumnConfig, sourceConfig) ?? true
      if (!canAcceptChild) {
        return false
      }
    }

    return true
  }, [operatorRegistry, getNodeConfig])

  // For child nodes, checks if the parent operator can accept more children
  const isValidSiblingDrop = useCallback((targetNode: TreeNode, dragInfo?: DropInfo): boolean => {
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

        if (dragInfo?.type === DragType.TREE_ITEM && dragInfo.data?.sourceParentKey === parentColumn.key) {
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
