/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useMemo, useState, useCallback, useRef, useEffect } from 'react'
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
  disabled?: boolean
  onEntityConfigChange: (config: QueryEntityConfig) => void
}

interface OperatorModalConfig {
  itemData: InternalTreeNode
  operatorId: string
}

interface AvailableFieldsTreeInnerProps {
  operatorModalConfig: OperatorModalConfig | null
  disabled: boolean
  setOperatorModalConfig: React.Dispatch<React.SetStateAction<OperatorModalConfig | null>>
}

const AvailableFieldsTreeInner = ({
  operatorModalConfig,
  disabled,
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
    fieldDefinitionRegistry,
    disabled
  })

  const allKeys = useMemo(
    () => collectAllKeys(items),
    [items]
  )

  const [expandedKeys, setExpandedKeys] = useState<React.Key[]>([])
  const prevAllKeysRef = useRef<string[]>([])

  useEffect(() => {
    const prevKeys = prevAllKeysRef.current
    const currentKeys = allKeys.map(String)

    if (prevKeys.length === 0 && currentKeys.length > 0) {
      prevAllKeysRef.current = currentKeys
      setExpandedKeys(currentKeys)
      return
    }

    if (currentKeys.length > prevKeys.length) {
      const newKeys = currentKeys.filter(k => !prevKeys.includes(k))
      const keysToExpand = new Set(expandedKeys.map(String))

      newKeys.forEach(newKey => {
        const path = findPath(newKey)
        if (path !== null && path.length > 1) {
          const parentPath = path.slice(0, -1)
          const parent = getItem(parentPath)
          if (parent?.isOperator === true) {
            keysToExpand.add(String(parent.key))
          }
        }
      })

      prevAllKeysRef.current = currentKeys
      setExpandedKeys(Array.from(keysToExpand))
      return
    }

    if (currentKeys.length < prevKeys.length) {
      const validKeys = expandedKeys.filter(k => currentKeys.includes(String(k)))
      prevAllKeysRef.current = currentKeys
      setExpandedKeys(validKeys)
      return
    }

    prevAllKeysRef.current = currentKeys
  }, [allKeys, expandedKeys, findPath, getItem])

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
      if (disabled) return
      deleteByKey(key)
      return
    }

    if (action === 'edit' || action === 'view') {
      const path = findPath(key)
      if (path === null) return

      const item = getItem(path)
      if (!item?.isOperator) return
      if (!isNonEmptyString(item.attributes.class)) return

      setOperatorModalConfig({
        itemData: item,
        operatorId: item.attributes.class
      })
    }
  }, [disabled, deleteByKey, findPath, getItem, setOperatorModalConfig])

  const expandedKeysSet = useMemo(() => new Set(expandedKeys), [expandedKeys])

  const titleRender = useCallback((node: TreeNodeData, initialComponent: React.ReactNode): React.JSX.Element => {
    const isExpanded = expandedKeysSet.has(node.key)
    const hasChildren = node.children !== undefined && node.children.length > 0
    const hasExpandedChildren = isExpanded && hasChildren

    return (
      <TreeNodeRenderer
        disabled={ disabled }
        hasExpandedChildren={ hasExpandedChildren }
        initialComponent={ initialComponent }
        itemData={ node.itemData }
      />
    )
  }, [expandedKeysSet, disabled])

  return (
    <>
      {items.length === 0
        ? (
            disabled
              ? <div style={ { padding: '16px', textAlign: 'center', color: '#999' } }>No fields configured</div>
              : <EmptyTreeDropZone />
          )
        : (
          <TreeElement
            blockNode
            className={ styles.treeContainer }
            defaultExpandedKeys={ expandedKeys }
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
          disabled,
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
  disabled = false,
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
      disabled={ disabled }
      entityConfig={ entityConfig }
      onEntityConfigChange={ onEntityConfigChange }
      onOperatorAdded={ handleOperatorAdded }
      operatorRegistryServiceId={ operatorRegistryServiceId }
    >
      <AvailableFieldsTreeInner
        disabled={ disabled }
        operatorModalConfig={ operatorModalConfig }
        setOperatorModalConfig={ setOperatorModalConfig }
      />
    </TreeProvider>
  )
}
