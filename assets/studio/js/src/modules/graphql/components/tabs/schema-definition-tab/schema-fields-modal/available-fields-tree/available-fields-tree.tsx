/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useMemo, useState, useCallback } from 'react'
import { Icon, TreeElement } from '@pimcore/studio-ui-bundle/components'
import { type QueryEntityConfig, type ColumnConfig } from '../types'
import { isNonEmptyString } from '@pimcore/studio-ui-bundle/utils'
import { isNil } from 'lodash'
import { type DynamicTypeOperatorRegistry } from '../../../../../../operators/dynamic-type-operator-registry'
import { type DynamicTypeFieldDefinitionRegistry } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { TreeProvider, useTreeContext } from './hooks/use-tree-context'
import { type TreeItemData, collectAllKeys, createTreeItem } from './tree-item'
import { TreeNodeRenderer } from './tree-node-renderer'
import { EmptyTreeDropZone } from './empty-tree-drop-zone'
import { useOperator } from '../../../../../../operators/hooks/use-operator'

interface AvailableFieldsTreeProps {
  entityConfig?: QueryEntityConfig
  operatorRegistryServiceId: string
  onEntityConfigChange: (config: QueryEntityConfig) => void
}

/**
 * Tree node type for antd TreeElement
 */
interface TreeNodeData {
  key: string
  title: React.ReactNode
  icon?: React.ReactNode
  children?: TreeNodeData[]
  className?: string
  actions?: Array<{ key: string, icon: string }>
  // Custom data for our use
  itemData: TreeItemData
}

/**
 * Build antd tree nodes from TreeItemData
 */
const buildTreeNodes = (
  items: TreeItemData[],
  operatorRegistry: DynamicTypeOperatorRegistry,
  fieldDefinitionRegistry: DynamicTypeFieldDefinitionRegistry,
  getLocalizedName: (operator: any) => string
): TreeNodeData[] => {
  const buildNode = (item: TreeItemData): TreeNodeData => {
    const treeItem = createTreeItem(item, operatorRegistry)

    let icon: React.ReactNode
    let title: React.ReactNode

    if (item.isOperator) {
      const operatorType = operatorRegistry.getDynamicType(String(item.attributes.class ?? ''), false)
      const config = { key: item.key, isOperator: true, attributes: item.attributes }
      icon = !isNil(operatorType)
        ? (
          <Icon
            { ...operatorType.getIcon() }
            iconColorGroup="operator"
          />
          )
        : undefined
      title = operatorType?.getLabel(config, getLocalizedName(operatorType)) ??
        item.attributes.label
    } else {
      const fieldDef = fieldDefinitionRegistry.getDynamicType(item.attributes.dataType ?? '', false)
      icon = !isNil(fieldDef)
        ? (
          <Icon
            { ...fieldDef.getIcon() }
            iconColorGroup="fieldDefinition"
          />
          )
        : undefined
      title = item.attributes.label ?? item.attributes.attribute ?? ''
    }

    const node: TreeNodeData = {
      key: item.key,
      title,
      icon,
      className: 'ant-tree-node--has-drag-and-drop',
      actions: treeItem.getActions(),
      itemData: item
    }

    if (Array.isArray(item.attributes.children) && item.attributes.children.length > 0) {
      node.children = item.attributes.children.map(buildNode)
    }

    return node
  }

  return items.map(buildNode)
}

interface OperatorModalConfig {
  itemData: TreeItemData
  operatorId: string
}

interface AvailableFieldsTreeInnerProps {
  operatorModalConfig: OperatorModalConfig | null
  setOperatorModalConfig: React.Dispatch<React.SetStateAction<OperatorModalConfig | null>>
}

/** Inner component that uses the context */
const AvailableFieldsTreeInner = ({
  operatorModalConfig,
  setOperatorModalConfig
}: AvailableFieldsTreeInnerProps): React.JSX.Element => {
  const {
    items,
    operatorRegistry,
    fieldDefinitionRegistry,
    deleteByKey,
    findPath,
    getItem,
    updateItemAttributes
  } = useTreeContext()

  const { getLocalizedName } = useOperator()

  // Build tree nodes for antd TreeElement
  const treeData = useMemo(
    () => buildTreeNodes(items, operatorRegistry, fieldDefinitionRegistry, getLocalizedName),
    [items, operatorRegistry, fieldDefinitionRegistry, getLocalizedName]
  )

  const allKeys = useMemo(
    () => collectAllKeys(items),
    [items]
  )

  const handleModalApply = useCallback((updatedConfig: ColumnConfig): void => {
    if (isNil(operatorModalConfig)) return

    // Update the item attributes in the tree
    updateItemAttributes(operatorModalConfig.itemData.key, updatedConfig.attributes)
    setOperatorModalConfig(null)
  }, [operatorModalConfig, updateItemAttributes, setOperatorModalConfig])

  const handleModalCancel = useCallback((): void => {
    setOperatorModalConfig(null)
  }, [setOperatorModalConfig])

  const handleActionsClick = useCallback((key: string, action: string): void => {
    if (action === 'delete') {
      deleteByKey(key)
      return
    }

    if (action === 'edit') {
      const path = findPath(key)
      if (path === null) return

      const item = getItem(path)
      if (item === null || !item.isOperator) return
      if (!isNonEmptyString(item.attributes.class)) return

      setOperatorModalConfig({
        itemData: item,
        operatorId: item.attributes.class
      })
    }
  }, [deleteByKey, findPath, getItem, setOperatorModalConfig])

  const titleRender = useCallback((node: TreeNodeData, initialComponent: React.ReactNode): React.JSX.Element => {
    return (
      <TreeNodeRenderer
        initialComponent={ initialComponent }
        itemData={ node.itemData }
      />
    )
  }, [])

  return (
    <>
      {items.length === 0
        ? (
          <EmptyTreeDropZone />
          )
        : (
          <TreeElement
            blockNode
            defaultExpandedKeys={ allKeys }
            onActionsClick={ handleActionsClick }
            selectable={ false }
            showIcon
            titleRender={ titleRender }
            treeData={ treeData }
          />
          )}

      {!isNil(operatorModalConfig) && (() => {
        const operatorType = operatorRegistry.getDynamicType(operatorModalConfig.operatorId, false)
        if (isNil(operatorType)) return null

        return operatorType.getConfigModal({
          config: {
            key: operatorModalConfig.itemData.key,
            isOperator: true,
            attributes: operatorModalConfig.itemData.attributes
          },
          operator: operatorType,
          onApply: handleModalApply,
          onCancel: handleModalCancel
        })
      })()}
    </>
  )
}

/** Main component that provides the context */
export const AvailableFieldsTree = ({
  entityConfig,
  operatorRegistryServiceId,
  onEntityConfigChange
}: AvailableFieldsTreeProps): React.JSX.Element => {
  const [operatorModalConfig, setOperatorModalConfig] = useState<OperatorModalConfig | null>(null)

  const handleOperatorAdded = useCallback((item: TreeItemData): void => {
    if (isNonEmptyString(item.attributes.class)) {
      setOperatorModalConfig({
        itemData: item,
        operatorId: item.attributes.class
      })
    }
  }, [])

  return (
    <TreeProvider
      entityConfig={ entityConfig }
      onEntityConfigChange={ onEntityConfigChange }
      onOperatorAdded={ handleOperatorAdded }
      operatorRegistryServiceId={ operatorRegistryServiceId }
    >
      <AvailableFieldsTreeInner
        operatorModalConfig={ operatorModalConfig }
        setOperatorModalConfig={ setOperatorModalConfig }
      />
    </TreeProvider>
  )
}
