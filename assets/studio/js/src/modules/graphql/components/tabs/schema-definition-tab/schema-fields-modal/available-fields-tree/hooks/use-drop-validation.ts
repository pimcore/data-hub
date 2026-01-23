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
  isValidDropIntoOperator: (targetNode: TreeNode) => boolean
  /** Check if a sibling drop is valid (for child nodes, checks parent constraints) */
  isValidSiblingDrop: (targetNode: TreeNode) => boolean
}

export const useDropValidation = ({
  columns,
  operatorRegistry
}: UseDropValidationProps): UseDropValidationReturn => {
  /**
   * Check if the drag type is valid for this tree
   */
  const isValidContext = useCallback((info: DropInfo, targetNodeKey: string): boolean => {
    const dropType = info.type
    
    // Only accept known drag types
    if (dropType !== 'class-attribute' && dropType !== 'operator' && dropType !== 'available-field') {
      return false
    }
    
    // Don't allow dropping on itself
    if (dropType === 'available-field' && info.data?.sourceKey === targetNodeKey) {
      return false
    }
    
    return true
  }, [])

  /**
   * Check if an operator can accept a child
   */
  const isValidDropIntoOperator = useCallback((targetNode: TreeNode): boolean => {
    // Only operators can have children
    if (targetNode.isOperator !== true) {
      return false
    }

    const operatorType = operatorRegistry.getDynamicType(
      targetNode.columnConfig?.attributes?.class ?? '',
      false
    )

    if (operatorType === undefined) {
      return false
    }

    // Check if operator type allows children at all
    const allowsChildrenAtAll = operatorType.allowsChildren?.() ?? false
    if (allowsChildrenAtAll !== true) {
      return false
    }

    // Look up the current column state to get the latest child count
    const currentColumn = columns.find(col => col.key === targetNode.columnConfig?.key)

    if (!isNil(currentColumn)) {
      // Check with the current column state (not the cached node.columnConfig)
      const canAcceptChild = operatorType.allowChild?.(currentColumn) ?? true
      if (canAcceptChild !== true) {
        return false
      }
    }

    return true
  }, [operatorRegistry, columns])

  /**
   * Check if a sibling drop is valid
   * For child nodes, this checks if the parent operator can accept more children
   */
  const isValidSiblingDrop = useCallback((targetNode: TreeNode): boolean => {
    // If this is a child node, check parent operator constraints
    if (targetNode.childIndex !== undefined && !isNil(targetNode.columnConfig)) {
      const parentColumn = columns.find(col => col.key === targetNode.columnConfig?.key)

      if (!isNil(parentColumn) && !isNil(parentColumn.attributes.class)) {
        const operatorType = operatorRegistry.getDynamicType(parentColumn.attributes.class, false)
        
        if (isNil(operatorType)) {
          return false
        }

        // Check if parent allows children at all
        if (operatorType.allowsChildren?.() === false) {
          return false
        }

        // Check if parent can accept more children
        const canAcceptChild = operatorType.allowChild?.(parentColumn) ?? true
        return canAcceptChild
      }
    }

    // For top-level nodes, allow the drop
    return true
  }, [columns, operatorRegistry])

  return {
    isValidContext,
    isValidDropIntoOperator,
    isValidSiblingDrop
  }
}
