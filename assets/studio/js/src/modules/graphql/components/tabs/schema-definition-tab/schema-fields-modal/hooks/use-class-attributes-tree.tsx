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
import { useClassDefinitionGetLayoutByIdQuery, type Layout } from '@pimcore/studio-ui-bundle/api/class-definition'
import { reduce, buildTree } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { type DynamicTypeFieldDefinitionRegistry, DynamicTypeFieldDefinitionDataAbstract } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { useInjection, serviceIds, useTranslation } from '@pimcore/studio-ui-bundle/app'
import { Icon } from '@pimcore/studio-ui-bundle/components'
import { isNil, flatMap } from 'lodash'
import { systemColumnDefinitions, SYSTEM_COLUMN_ICON } from '../definitions/system-column-definitions'
import { type TreeNode } from '../types'
import { useObjectBrickLayouts } from './use-objectbrick-layouts'
import {
  filterTreeNodesRecursive,
  collectAllKeys,
  scanForObjectBricksFields,
  removeObjectBricksNodes
} from './class-attributes-tree-helpers'

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

export const useClassAttributesTree = ({
  classId,
  enabled,
  searchValue = ''
}: UseClassAttributesTreeProps): UseClassAttributesTreeReturn => {
  const fieldDefinitionRegistry = useInjection<DynamicTypeFieldDefinitionRegistry>(serviceIds['DynamicTypes/FieldDefinitionRegistry'])
  const { t } = useTranslation()

  const { data: classLayout, isLoading, isFetching } = useClassDefinitionGetLayoutByIdQuery(
    { id: classId },
    { skip: !enabled, refetchOnMountOrArgChange: true }
  )

  const objectBricksFields = useMemo(() => {
    if (isNil(classLayout)) return []
    return scanForObjectBricksFields(classLayout)
  }, [classLayout])

  const allBrickKeys = useMemo((): string[] => {
    return Array.from(new Set<string>(flatMap(objectBricksFields, f => f.allowedTypes)))
  }, [objectBricksFields])

  const { layouts: brickLayouts, isLoading: brickLayoutsLoading } = useObjectBrickLayouts(allBrickKeys)

  const classAttributesTree = useMemo(() => {
    const buildItemCallback = (brickKey?: string) => (
      { fieldDefinition, initialTreeItem }: { fieldDefinition: any, initialTreeItem: any }
    ): any => {
      const dynType = fieldDefinitionRegistry.getDynamicType(fieldDefinition.fieldtype as string, false)
      const isFieldDefinition = dynType instanceof DynamicTypeFieldDefinitionDataAbstract

      const { icon: _icon, ...restTreeItem } = initialTreeItem

      const label = fieldDefinition.title ?? fieldDefinition.name

      if (!isNil(brickKey) && isFieldDefinition) {
        const attributeKey = `${brickKey}~${fieldDefinition.name}`
        const title = `${label} (${brickKey}.${fieldDefinition.name})`

        return {
          ...restTreeItem,
          title,
          className: 'ant-tree-node--has-drag-and-drop',
          icon: initialTreeItem.icon,
          dataType: fieldDefinition.fieldtype,
          attribute: attributeKey,
          iconProps: dynType?.getIcon() ?? { value: 'info' },
          isFieldDefinition: true
        }
      }

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

    const buildDataObjectColumns = (): TreeNode['children'] => {
      if (isNil(classLayout)) return []

      const reduced = reduce({ layout: classLayout })
      if (reduced?.structure === undefined) return []

      const { structure, fieldDefinitions } = reduced
      const tree = buildTree({
        structure,
        fieldDefinitions,
        itemCallback: buildItemCallback()
      })

      const children = (tree?.children ?? []) as TreeNode[]

      return removeObjectBricksNodes(children)
    }

    const buildBrickGroupNodes = (): TreeNode[] => {
      if (isNil(classLayout) || brickLayouts.size === 0) return []

      return flatMap(allBrickKeys, brickKey => {
        const brickLayout = brickLayouts.get(brickKey)
        if (isNil(brickLayout)) return []

        // Cast: ConfigLayoutDefinition is structurally compatible with Layout for reduce/buildTree
        const reduced = reduce({ layout: brickLayout as unknown as Layout })
        if (isNil(reduced?.structure)) return []

        const { structure, fieldDefinitions } = reduced
        const brickTree = buildTree({
          structure,
          fieldDefinitions,
          itemCallback: buildItemCallback(brickKey)
        })

        const brickGroupNode: TreeNode = {
          key: `brick-group-${brickKey}`,
          title: `${brickKey} Columns`,
          isLeaf: false,
          icon: <Icon value="object-bricks" />,
          iconProps: { value: 'object-bricks' },
          // Use brickTree.children directly to skip the redundant root Panel node
          children: (brickTree?.children ?? []) as TreeNode[]
        }

        return [brickGroupNode]
      })
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

      return [dataObjectColumns, buildSystemColumns(), ...buildBrickGroupNodes()]
    } catch (error) {
      console.error('Error building class attributes tree:', error)
      return []
    }
  }, [classLayout, brickLayouts, allBrickKeys, fieldDefinitionRegistry, t])

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
    return collectAllKeys(filteredTree)
  }, [searchValue, classAttributesTree, filteredTree])

  const getFieldDefinitions = useCallback((): TreeNode[] => {
    const collectFieldDefinitions = (nodes: TreeNode[]): TreeNode[] =>
      flatMap(nodes, node => {
        if (node.isFieldDefinition === true) return [node]
        if (!isNil(node.children)) return collectFieldDefinitions(node.children as TreeNode[])
        return []
      })

    const objectColumnsNode = classAttributesTree.find(node => node.key === 'object-columns')
    const objectColumnDefs = (!isNil(objectColumnsNode) && !isNil(objectColumnsNode.children))
      ? collectFieldDefinitions(objectColumnsNode.children as TreeNode[])
      : []

    const brickGroupNodes = classAttributesTree.filter(
      node => typeof node.key === 'string' && node.key.startsWith('brick-group-')
    )
    const brickColumnDefs = collectFieldDefinitions(brickGroupNodes)

    return [...objectColumnDefs, ...brickColumnDefs]
  }, [classAttributesTree])

  // Consider still loading if the class layout resolved but we know there are brick keys
  // to fetch and the brick layouts haven't all been resolved yet. This prevents the
  // intermediate flash between "class layout done" and "brick fetch effect fires".
  const pendingBrickFetch = !isLoading && !isFetching && allBrickKeys.length > 0 && brickLayouts.size === 0

  return {
    classAttributesTree,
    filteredTree,
    expandedKeys,
    getFieldDefinitions,
    isLoading: isLoading || isFetching || brickLayoutsLoading || pendingBrickFetch
  }
}
