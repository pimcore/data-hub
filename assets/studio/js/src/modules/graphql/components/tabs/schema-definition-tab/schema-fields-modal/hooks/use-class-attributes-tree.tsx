/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useMemo, useCallback } from 'react'
import { useClassDefinitionGetLayoutByIdQuery } from '@pimcore/studio-ui-bundle/api/class-definition'
import { reduce, buildTree } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { type DynamicTypeFieldDefinitionRegistry, DynamicTypeFieldDefinitionDataAbstract } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { useInjection, serviceIds, useTranslation } from '@pimcore/studio-ui-bundle/app'
import { Icon } from '@pimcore/studio-ui-bundle/components'
import { isNil, flatMap, flatMapDeep } from 'lodash'
import { systemColumnDefinitions, SYSTEM_COLUMN_ICON } from '../definitions/system-column-definitions'
import { type TreeNode } from '../types'

interface UseClassAttributesTreeProps {
  classId: string
  enabled: boolean
  searchValue?: string
}

interface UseClassAttributesTreeReturn {
  classAttributesTree: TreeNode[]
  filteredTree: TreeNode[]
  expandedKeys: string[]
  getFieldDefinitions: () => TreeNode[]
  isLoading: boolean
}

const filterTreeNodesRecursive = (
  nodes: TreeNode[],
  searchValue: string
): TreeNode[] => {
  if (!Array.isArray(nodes)) {
    return []
  }

  return nodes.reduce<TreeNode[]>((acc, node) => {
    // Only search in field definitions (isFieldDefinition: true)
    const isFieldDefinition = node.isFieldDefinition === true
    const matchesSearch = isFieldDefinition &&
      ((node.title?.toString().toLowerCase().includes(searchValue.toLowerCase()) ?? false) ||
       (node.attribute?.toLowerCase().includes(searchValue.toLowerCase()) ?? false))

    const filteredChildren = (!isNil(node.children) && Array.isArray(node.children))
      ? filterTreeNodesRecursive(node.children as TreeNode[], searchValue)
      : []

    // Include node if:
    // 1. It's a field definition and matches the search
    // 2. It has children that match the search (parent nodes)
    if ((matchesSearch ?? false) || filteredChildren.length > 0) {
      acc.push({
        ...node,
        children: filteredChildren.length > 0 ? filteredChildren : node.children
      })
    }

    return acc
  }, [])
}

const collectAllKeys = (nodes: TreeNode[]): string[] => {
  return flatMapDeep(nodes, (node) => [
    ...(isNil(node.key) ? [] : [String(node.key)]),
    ...(!isNil(node.children) && Array.isArray(node.children) ? collectAllKeys(node.children as TreeNode[]) : [])
  ])
}

export const useClassAttributesTree = ({
  classId,
  enabled,
  searchValue = ''
}: UseClassAttributesTreeProps): UseClassAttributesTreeReturn => {
  const fieldDefinitionRegistry = useInjection<DynamicTypeFieldDefinitionRegistry>(serviceIds['DynamicTypes/FieldDefinitionRegistry'])
  const { t } = useTranslation()

  const { data: classLayout, isLoading, isFetching } = useClassDefinitionGetLayoutByIdQuery(
    { id: classId },
    { skip: !enabled }
  )

  const classAttributesTree = useMemo(() => {
    const buildDataObjectColumns = (): TreeNode['children'] => {
      if (isNil(classLayout)) return []

      const reduced = reduce({ layout: classLayout })
      if (reduced?.structure === undefined) return []

      const { structure, fieldDefinitions } = reduced
      const tree = buildTree({
        structure,
        fieldDefinitions,
        itemCallback: ({ fieldDefinition, initialTreeItem }) => {
          const dynType = fieldDefinitionRegistry.getDynamicType(fieldDefinition.fieldtype, false)
          const isFieldDefinition = dynType instanceof DynamicTypeFieldDefinitionDataAbstract

          const { icon: _icon, ...restTreeItem } = initialTreeItem

          const label = fieldDefinition.title ?? fieldDefinition.name
          const title = isFieldDefinition
            ? `${label} (${fieldDefinition.name})`
            : initialTreeItem.title

          return {
            ...restTreeItem,
            title,
            className: isFieldDefinition ? 'ant-tree-node--has-drag-and-drop' : undefined,
            icon: initialTreeItem.icon,
            dataType: fieldDefinition.fieldtype,
            attribute: fieldDefinition.name,
            iconProps: dynType?.getIcon() ?? { value: 'info' },
            isFieldDefinition
          }
        }
      })

      return (tree?.children ?? []) as TreeNode[]
    }

    const buildSystemColumns = (): TreeNode => ({
      key: 'system-columns',
      title: t('data-hub.schema.system-columns'),
      isLeaf: false,
      icon: <Icon { ...SYSTEM_COLUMN_ICON } />,
      iconProps: SYSTEM_COLUMN_ICON,
      children: systemColumnDefinitions.map(({ key, attribute, translationKey, iconProps }) => ({
        key,
        title: t(translationKey),
        isLeaf: true,
        dataType: 'system',
        icon: <Icon { ...iconProps } />,
        iconProps,
        className: 'ant-tree-node--has-drag-and-drop',
        attribute,
        isFieldDefinition: true
      }))
    })

    try {
      const dataObjectColumnsData = buildDataObjectColumns()
      const dataObjectColumns = {
        key: 'object-columns',
        title: t('data-hub.schema.data-object-columns'),
        isLeaf: false,
        icon: <Icon value="data-object" />,
        iconProps: { value: 'data-object' },
        children: dataObjectColumnsData
      }

      return [dataObjectColumns, buildSystemColumns()]
    } catch (error) {
      console.error('Error building class attributes tree:', error)
      return []
    }
  }, [classLayout])

  const filteredTree = useMemo(() => {
    if (searchValue === '') {
      return classAttributesTree
    }
    return filterTreeNodesRecursive(classAttributesTree, searchValue)
  }, [searchValue, classAttributesTree])

  const expandedKeys = useMemo(() => {
    if (searchValue === '') {
      return collectAllKeys(classAttributesTree)
    }
    // When searching, expand all nodes to show matched results
    return collectAllKeys(filteredTree)
  }, [searchValue, classAttributesTree, filteredTree])

  const getFieldDefinitions = useCallback((): TreeNode[] => {
    const objectColumnsNode = classAttributesTree.find(node => node.key === 'object-columns')
    if (isNil(objectColumnsNode) || isNil(objectColumnsNode.children)) return []

    const collectFieldDefinitions = (nodes: TreeNode[]): TreeNode[] =>
      flatMap(nodes, node => {
        if (node.isFieldDefinition === true) return [node]
        if (!isNil(node.children)) return collectFieldDefinitions(node.children as TreeNode[])
        return []
      })

    return collectFieldDefinitions(objectColumnsNode.children as TreeNode[])
  }, [classAttributesTree])

  return {
    classAttributesTree,
    filteredTree,
    expandedKeys,
    getFieldDefinitions,
    isLoading: isLoading || isFetching
  }
}
