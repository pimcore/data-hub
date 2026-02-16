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
import { TreeElement } from '@pimcore/studio-ui-bundle/components'
import { type QueryEntityConfig, type PersistedColumnConfig } from '../types'
import { isNonEmptyString } from '@pimcore/studio-ui-bundle/utils'
import { isNil } from 'lodash'
import { TreeProvider, useTreeContext } from './hooks/use-tree-context'
import { type InternalTreeNode } from './tree-item/tree-item'
import { collectAllKeys } from './utils/tree-operations'
import { TreeNodeRenderer } from './tree-node-renderer'
import { EmptyTreeDropZone } from './empty-tree-drop-zone'
import { useStyles } from './available-fields-tree.styles'
import { useTreeNodes, type TreeNodeData } from './hooks/use-tree-nodes'

interface AvailableFieldsTreeProps {
  entityConfig?: QueryEntityConfig
  operatorRegistryServiceId: string
  onEntityConfigChange: (config: QueryEntityConfig) => void
}

interface OperatorModalConfig {
  itemData: InternalTreeNode
  operatorId: string
}

interface AvailableFieldsTreeInnerProps {
  operatorModalConfig: OperatorModalConfig | null
  setOperatorModalConfig: React.Dispatch<React.SetStateAction<OperatorModalConfig | null>>
}

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

  const { styles } = useStyles()

  const treeData = useTreeNodes({
    items,
    operatorRegistry,
    fieldDefinitionRegistry
  })

  const allKeys = useMemo(
    () => collectAllKeys(items),
    [items]
  )

  const [expandedKeys, setExpandedKeys] = useState<React.Key[]>(allKeys)

  useMemo(() => {
    setExpandedKeys(allKeys)
  }, [allKeys])

  const handleExpand = useCallback((keys: React.Key[]): void => {
    setExpandedKeys(keys)
  }, [])

  const handleModalApply = useCallback((updatedConfig: PersistedColumnConfig): void => {
    if (isNil(operatorModalConfig)) return

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

  const expandedKeysSet = useMemo(() => new Set(expandedKeys), [expandedKeys])

  const titleRender = useCallback((node: TreeNodeData, initialComponent: React.ReactNode): React.JSX.Element => {
    const isExpanded = expandedKeysSet.has(node.key)
    const hasChildren = node.children !== undefined && node.children.length > 0
    const hasExpandedChildren = isExpanded && hasChildren

    return (
      <TreeNodeRenderer
        hasExpandedChildren={ hasExpandedChildren }
        initialComponent={ initialComponent }
        itemData={ node.itemData }
      />
    )
  }, [expandedKeysSet])

  return (
    <>
      {items.length === 0
        ? (
          <EmptyTreeDropZone />
          )
        : (
          <TreeElement
            blockNode
            className={ styles.treeContainer }
            expandedKeys={ expandedKeys }
            onActionsClick={ handleActionsClick }
            onExpand={ handleExpand }
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

export const AvailableFieldsTree = ({
  entityConfig,
  operatorRegistryServiceId,
  onEntityConfigChange
}: AvailableFieldsTreeProps): React.JSX.Element => {
  const [operatorModalConfig, setOperatorModalConfig] = useState<OperatorModalConfig | null>(null)

  const handleOperatorAdded = useCallback((item: InternalTreeNode): void => {
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
