/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import type { TreeNode } from '../types'

// The backend has no column for these field types; their children are columns of their own (see DataObjectFieldHelper::getFieldDefinitionFromKey()).
const ATTRIBUTE_CONTAINER_FIELD_TYPES = new Set(['localizedfields'])

export const isAttributeContainer = (fieldType: unknown): boolean =>
  typeof fieldType === 'string' && ATTRIBUTE_CONTAINER_FIELD_TYPES.has(fieldType)

export const collectLocalizedFieldNames = (layout: { children?: object[] }): Set<string> => {
  const names = new Set<string>()

  const walk = (children: object[], inLocalizedFields: boolean): void => {
    children.forEach(child => {
      const node = child as Record<string, unknown>

      if (inLocalizedFields && node.datatype === 'data' && typeof node.name === 'string') {
        names.add(node.name)
      }

      if (Array.isArray(node.children)) {
        walk(node.children as object[], inLocalizedFields || node.fieldtype === 'localizedfields')
      }
    })
  }

  walk(layout.children ?? [], false)

  return names
}

export interface AttributeTreeItemProps {
  fieldDefinition: { name?: string, title?: string | null, fieldtype?: string }
  initialTreeItem: TreeNode
  isDataField: boolean
  iconProps: unknown
  brickColumn?: (label: string) => { attribute: string, title: string }
}

export const buildAttributeTreeItem = ({
  fieldDefinition,
  initialTreeItem,
  isDataField,
  iconProps,
  brickColumn
}: AttributeTreeItemProps): TreeNode => {
  const isFieldDefinition = isDataField && !isAttributeContainer(fieldDefinition.fieldtype)
  const { icon, ...restTreeItem } = initialTreeItem
  const label = fieldDefinition.title ?? fieldDefinition.name ?? ''

  const column = isFieldDefinition ? brickColumn?.(label) : undefined
  if (column !== undefined) {
    return {
      ...restTreeItem,
      title: column.title,
      className: 'ant-tree-node--has-drag-and-drop',
      icon,
      dataType: fieldDefinition.fieldtype,
      attribute: column.attribute,
      iconProps,
      isFieldDefinition: true
    }
  }

  return {
    ...restTreeItem,
    title: isFieldDefinition ? `${label} (${fieldDefinition.name})` : initialTreeItem.title,
    className: isFieldDefinition ? 'ant-tree-node--has-drag-and-drop' : undefined,
    icon,
    dataType: fieldDefinition.fieldtype,
    attribute: fieldDefinition.name,
    iconProps,
    isFieldDefinition
  }
}

const collectFieldDefinitions = (nodes: TreeNode[]): TreeNode[] =>
  nodes.reduce<TreeNode[]>((columns, node) => {
    if (node.isFieldDefinition === true) return [...columns, node]
    if (Array.isArray(node.children)) return [...columns, ...collectFieldDefinitions(node.children as TreeNode[])]
    return columns
  }, [])

// The columns "Insert all definitions" adds: those of the class and of its bricks, not the system columns.
export const collectColumnFieldDefinitions = (classAttributesTree: TreeNode[]): TreeNode[] => {
  const objectColumnsNode = classAttributesTree.find(node => node.key === 'object-columns')
  const brickContainerNodes = classAttributesTree.filter(
    node => typeof node.key === 'string' && node.key.startsWith('brick-container-')
  )

  return [
    ...collectFieldDefinitions((objectColumnsNode?.children ?? []) as TreeNode[]),
    ...collectFieldDefinitions(brickContainerNodes)
  ]
}
