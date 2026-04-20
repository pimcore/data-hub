/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type Layout } from '@pimcore/studio-ui-bundle/api/class-definition'
import { isNil, flatMapDeep } from 'lodash'
import { type TreeNode } from '../types'

export interface ObjectBricksFieldInfo {
  name: string
  title: string | null
  allowedTypes: string[]
}

export const filterTreeNodesRecursive = (
  nodes: TreeNode[],
  searchValue: string
): TreeNode[] => {
  if (!Array.isArray(nodes)) {
    return []
  }

  return nodes.reduce<TreeNode[]>((acc, node) => {
    const isFieldDefinition = node.isFieldDefinition === true
    const matchesSearch = isFieldDefinition &&
      ((node.title?.toString().toLowerCase().includes(searchValue.toLowerCase()) ?? false) ||
       (node.attribute?.toLowerCase().includes(searchValue.toLowerCase()) ?? false))

    const filteredChildren = (!isNil(node.children) && Array.isArray(node.children))
      ? filterTreeNodesRecursive(node.children as TreeNode[], searchValue)
      : []

    if ((matchesSearch ?? false) || filteredChildren.length > 0) {
      acc.push({
        ...node,
        children: filteredChildren.length > 0 ? filteredChildren : node.children
      })
    }

    return acc
  }, [])
}

export const collectAllKeys = (nodes: TreeNode[]): string[] => {
  return flatMapDeep(nodes, (node) => [
    ...(isNil(node.key) ? [] : [String(node.key)]),
    ...(!isNil(node.children) && Array.isArray(node.children) ? collectAllKeys(node.children as TreeNode[]) : [])
  ])
}

/**
 * Recursively walks a raw layout's children to collect all objectbricks fields
 * that have a non-empty allowedTypes list.
 */
export const scanForObjectBricksFields = (layout: Layout): ObjectBricksFieldInfo[] => {
  const results: ObjectBricksFieldInfo[] = []

  const walk = (children: object[]): void => {
    children.forEach(child => {
      const node = child as Record<string, unknown>

      if (node.fieldtype === 'objectbricks') {
        const allowedTypes = (node.allowedTypes as string[] | undefined) ?? []
        if (allowedTypes.length > 0) {
          results.push({
            name: node.name as string,
            title: (node.title as string | null | undefined) ?? null,
            allowedTypes
          })
        }
      }

      if (Array.isArray(node.children)) {
        walk(node.children as object[])
      }
    })
  }

  if (Array.isArray(layout.children)) {
    walk(layout.children)
  }

  return results
}

/**
 * Recursively removes objectbricks field nodes from the tree.
 * They are replaced by brick group nodes appended at the root level.
 */
export const removeObjectBricksNodes = (nodes: TreeNode[]): TreeNode[] => {
  return nodes
    .filter(node => !(node.dataType === 'objectbricks' && node.isFieldDefinition === true))
    .map(node => ({
      ...node,
      children: (!isNil(node.children) && Array.isArray(node.children))
        ? removeObjectBricksNodes(node.children as TreeNode[])
        : node.children
    }))
}
