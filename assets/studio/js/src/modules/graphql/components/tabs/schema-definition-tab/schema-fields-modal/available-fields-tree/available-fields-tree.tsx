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
import { type TreeNode, type QueryEntityConfig, type ColumnConfig } from '../types'
import { isNonEmptyString } from '@pimcore/studio-ui-bundle/utils'
import { isNil } from 'lodash'
import { type DynamicTypeOperatorRegistry } from '../../../../../../operators/dynamic-type-operator-registry'
import { type DynamicTypeFieldDefinitionRegistry } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { TreeNodeTitleRenderer } from './tree-node-title-renderer'
import { AvailableFieldsProvider, useAvailableFieldsContext } from './available-fields-context'

interface AvailableFieldsTreeProps {
  className?: string
  entityConfig?: QueryEntityConfig
  entityName: string
  operatorRegistryServiceId: string
  onEntityConfigChange: (config: QueryEntityConfig) => void
}

const buildTreeFromColumns = (columns: ColumnConfig[], operatorRegistry: DynamicTypeOperatorRegistry, fieldDefinitionRegistry: DynamicTypeFieldDefinitionRegistry): TreeNode[] => {
  if (columns.length === 0) {
    return []
  }

  const fieldNodes: TreeNode[] = columns.map((column, index) => {
    const { attributes, isOperator, key } = column
    const nodeKey = key!

    if (isOperator && isNonEmptyString(attributes.class)) {
      const operatorType = operatorRegistry.getDynamicType(attributes.class, false)

      const operatorNode: TreeNode = {
        key: nodeKey,
        title: operatorType?.getLabel(column) ?? attributes.label,
        dataType: attributes.dataType,
        isOperator,
        icon: operatorType !== undefined
          ? (
            <Icon
              { ...operatorType.getIcon() }
              iconColorGroup="operator"
            />
            )
          : undefined,
        columnConfig: column,
        className: 'ant-tree-node--has-drag-and-drop',
        actions: [
          { key: 'edit', icon: 'edit' },
          { key: 'delete', icon: 'trash' }
        ]
      }

      if (Array.isArray(attributes.children)) {
        const children: TreeNode[] = attributes.children.map((child: any, childIndex: number) => {
          const childAttrs = child.attributes ?? child
          const dataType = String(childAttrs.dataType ?? '')
          const label = String(childAttrs.label ?? '')
          const attribute = String(childAttrs.attribute ?? '')
          const fieldDef = fieldDefinitionRegistry.getDynamicType(dataType, false)
          const childKey = String(child.key ?? '')
          return {
            key: childKey,
            title: label !== '' ? label : (attribute !== '' ? attribute : ''),
            dataType,
            icon: fieldDef !== undefined
              ? (
                <Icon
                  { ...fieldDef?.getIcon() }
                  iconColorGroup="fieldDefinition"
                />
                )
              : undefined,
            columnConfig: column,
            childIndex,
            className: 'ant-tree-node--has-drag-and-drop',
            actions: [
              { key: 'delete', icon: 'trash' }
            ]
          }
        })
        operatorNode.children = children
      }

      return operatorNode
    }

    const fieldDefinition = fieldDefinitionRegistry.getDynamicType(attributes.dataType ?? '', false)
    return {
      key: nodeKey,
      title: attributes.label,
      dataType: attributes.dataType,
      isOperator: false,
      icon: fieldDefinition !== undefined
        ? (
          <Icon
            { ...fieldDefinition?.getIcon() }
            iconColorGroup="fieldDefinition"
          />
          )
        : undefined,
      columnConfig: column,
      className: 'ant-tree-node--has-drag-and-drop',
      actions: [
        { key: 'delete', icon: 'trash' }
      ],
      children: undefined
    }
  })

  return fieldNodes
}

/** Inner component that uses the context */
const AvailableFieldsTreeInner = (): React.JSX.Element => {
  const {
    columns,
    treeData,
    operatorRegistry,
    updateColumns,
    findColumnByKey,
    deleteItem
  } = useAvailableFieldsContext()

  const [operatorModalConfig, setOperatorModalConfig] = useState<{
    column: ColumnConfig
    operatorId: string
    columnIndex: number
  } | null>(null)

  const handleModalApply = useCallback((updatedConfig: ColumnConfig): void => {
    if (isNil(operatorModalConfig)) return

    const newColumns = [...columns]
    newColumns[operatorModalConfig.columnIndex] = updatedConfig

    updateColumns(newColumns)
    setOperatorModalConfig(null)
  }, [operatorModalConfig, columns, updateColumns])

  const handleModalCancel = useCallback((): void => {
    setOperatorModalConfig(null)
  }, [])

  const handleActionsClick = useCallback((key: string, action: string): void => {
    if (action === 'delete') {
      deleteItem(key)
      return
    }

    if (action === 'edit') {
      const found = findColumnByKey(key)
      if (found === undefined) return
      
      // Only top-level operators can be edited (no childIndex means it's top-level)
      if (found.childIndex !== undefined) return
      
      if (!isNonEmptyString(found.column.attributes.class)) return

      const columnIndex = columns.findIndex(c => c.key === found.column.key)
      if (columnIndex === -1) return

      setOperatorModalConfig({
        column: columns[columnIndex],
        operatorId: found.column.attributes.class,
        columnIndex
      })
    }
  }, [columns, deleteItem, findColumnByKey])

  const allKeys = useMemo(() => {
    const keys: string[] = []
    const collectKeys = (nodes: TreeNode[]): void => {
      nodes.forEach(node => {
        keys.push(String(node.key))
        if (Array.isArray(node.children) && node.children.length > 0) {
          collectKeys(node.children)
        }
      })
    }
    collectKeys(treeData)
    return keys
  }, [treeData])

  const titleRender = useCallback((node: TreeNode, initialComponent: React.ReactNode): React.JSX.Element => {
    return (
      <TreeNodeTitleRenderer
        initialComponent={ initialComponent }
        node={ node }
      />
    )
  }, [])

  return (
    <>
      <TreeElement
        blockNode
        defaultExpandedKeys={ allKeys }
        onActionsClick={ handleActionsClick }
        selectable={ false }
        showIcon
        titleRender={ titleRender }
        treeData={ treeData }
      />

      {!isNil(operatorModalConfig) && (() => {
        const operatorType = operatorRegistry.getDynamicType(operatorModalConfig.operatorId, false)
        if (operatorType === undefined) return null

        return operatorType.getConfigModal({
          config: operatorModalConfig.column,
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
  return (
    <AvailableFieldsProvider
      buildTreeFromColumns={ buildTreeFromColumns }
      entityConfig={ entityConfig }
      onEntityConfigChange={ onEntityConfigChange }
      operatorRegistryServiceId={ operatorRegistryServiceId }
    >
      <AvailableFieldsTreeInner />
    </AvailableFieldsProvider>
  )
}
