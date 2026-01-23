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
  isValidDropIntoOperator: (targetNode: TreeNode, dragInfo?: DropInfo) => boolean
  /** Check if a sibling drop is valid (for child nodes, checks parent constraints) */
  isValidSiblingDrop: (targetNode: TreeNode, dragInfo?: DropInfo) => boolean
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
   * Helper to get the actual config for a node (handles nested operators)
   */
  const getNodeConfig = useCallback((node: TreeNode): any => {
    // For top-level nodes, return the column config directly
    if (node.childIndex === undefined) {
      const column = columns.find(col => col.key === node.columnConfig?.key)
      return column
    }

    // For nested nodes, we need to traverse to find the actual config
    // Start with the parent column
    const parentColumn = columns.find(col => col.key === node.columnConfig?.key)
    if (!parentColumn || !Array.isArray(parentColumn.attributes?.children)) {
      return null
    }

    // Use the key to find the exact child in the tree
    // The key is unique, so we can search recursively
    const findChildByKey = (children: any[], targetKey: string): any => {
      for (const child of children) {
        if (child.key === targetKey) {
          return child
        }
        if (Array.isArray(child.attributes?.children)) {
          const found = findChildByKey(child.attributes.children, targetKey)
          if (found) return found
        }
      }
      return null
    }

    return findChildByKey(parentColumn.attributes.children, String(node.key))
  }, [columns])

  /**
   * Check if an operator can accept a child
   */
  const isValidDropIntoOperator = useCallback((targetNode: TreeNode, dragInfo?: DropInfo): boolean => {
    // Field definitions cannot have children - they must always be leafs
    if (targetNode.isOperator !== true) {
      return false
    }

    const operatorClass = targetNode.childIndex !== undefined
      ? targetNode.columnConfig?.attributes?.children?.[targetNode.childIndex]?.attributes?.class
      : targetNode.columnConfig?.attributes?.class

    const operatorType = operatorRegistry.getDynamicType(
      operatorClass ?? '',
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

    // Get the actual config for this node (handles nested operators)
    const currentConfig = getNodeConfig(targetNode)

    if (!isNil(currentConfig)) {
      // Check with the current config state
      const canAcceptChild = operatorType.allowChild?.(currentConfig) ?? true
      if (canAcceptChild !== true) {
        return false
      }
    }

    return true
  }, [operatorRegistry, getNodeConfig])

  /**
   * Check if a sibling drop is valid
   * For child nodes, this checks if the parent operator can accept more children
   */
  const isValidSiblingDrop = useCallback((targetNode: TreeNode, dragInfo?: DropInfo): boolean => {
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

        // For move operations within the same parent, we're not adding new children
        // so we don't need to check allowChild
        if (dragInfo?.type === 'available-field' && dragInfo.data?.sourceParentKey === parentColumn.key) {
          return true
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
