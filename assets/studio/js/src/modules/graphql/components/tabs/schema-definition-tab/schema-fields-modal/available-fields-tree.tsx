/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useMemo, useState } from 'react'
import { Icon, TreeElement } from '@pimcore/studio-ui-bundle/components'
import { type TreeNode, type QueryEntityConfig, type ColumnConfig } from './types'
import { useInjection, serviceIds } from '@pimcore/studio-ui-bundle/app'
import { uuid, isNonEmptyString } from '@pimcore/studio-ui-bundle/utils'
import { isNil } from 'lodash'
import { type DynamicTypeOperatorRegistry } from '../../../../operator-types/dynamic-type-operator-registry'
import { type DynamicTypeFieldDefinitionRegistry } from '@pimcore/studio-ui-bundle/modules/field-definitions'

interface AvailableFieldsTreeProps {
  className?: string
  entityConfig?: QueryEntityConfig
  entityName: string
  onEntityConfigChange: (config: QueryEntityConfig) => void
}

const enrichConfigWithUUIDs = (config: QueryEntityConfig): QueryEntityConfig => {
  if (config?.columnConfig?.columns === undefined) {
    return config
  }

  const enrichedColumns = config.columnConfig.columns.map(column => {
    if (!isNonEmptyString(column.key)) {
      column.key = uuid()
    }

    if (Array.isArray(column.attributes.children)) {
      column.attributes.children = column.attributes.children.map(child => {
        if (!isNonEmptyString(child.key)) {
          child.key = uuid()
        }
        return child
      })
    }

    return column
  })

  return {
    ...config,
    columnConfig: {
      ...config.columnConfig,
      columns: enrichedColumns
    }
  }
}

const buildTreeFromColumns = (config: QueryEntityConfig | undefined, operatorRegistry: DynamicTypeOperatorRegistry, fieldDefinitionRegistry: DynamicTypeFieldDefinitionRegistry): TreeNode[] => {
  if (config?.columnConfig?.columns === undefined) {
    return []
  }

  const fieldNodes: TreeNode[] = config.columnConfig.columns.map((column, index) => {
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
        actions: [
          { key: 'edit', icon: 'pencil-01' },
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
      actions: [
        { key: 'delete', icon: 'trash' }
      ],
      children: undefined
    }
  })

  return fieldNodes
}

export const AvailableFieldsTree = ({ entityConfig, entityName, onEntityConfigChange }: AvailableFieldsTreeProps): React.JSX.Element => {
  const [operatorModalConfig, setOperatorModalConfig] = useState<{
    column: ColumnConfig
    operatorId: string
    columnIndex: number
  } | null>(null)
  const operatorRegistry = useInjection<DynamicTypeOperatorRegistry>('DataHub/DynamicTypes/Operator/Registry')
  const fieldDefinitionRegistry = useInjection<DynamicTypeFieldDefinitionRegistry>(serviceIds['DynamicTypes/FieldDefinitionRegistry'])

  const enrichedConfig = useMemo(() => {
    if (entityConfig === undefined) return entityConfig
    return enrichConfigWithUUIDs(entityConfig)
  }, [entityConfig])

  const updateColumns = (columns: ColumnConfig[]): void => {
    if (isNil(enrichedConfig)) return

    const updatedEntity = {
      ...enrichedConfig,
      columnConfig: {
        ...enrichedConfig.columnConfig,
        columns
      }
    }

    onEntityConfigChange(updatedEntity)
  }

  const handleModalApply = (updatedConfig: ColumnConfig): void => {
    if (isNil(operatorModalConfig) || isNil(enrichedConfig)) return

    const columns = [...(enrichedConfig.columnConfig?.columns ?? [])]
    columns[operatorModalConfig.columnIndex] = updatedConfig

    updateColumns(columns)
    setOperatorModalConfig(null)
  }

  const handleModalCancel = (): void => {
    setOperatorModalConfig(null)
  }

  const handleActionsClick = (key: string, action: string): void => {
    const findTreeNode = (nodes: TreeNode[], searchKey: string): TreeNode | null => {
      for (const treeNode of nodes) {
        if (treeNode.key === searchKey) {
          return treeNode
        }
        if (treeNode.children !== undefined) {
          const found = findTreeNode(treeNode.children, searchKey)
          if (!isNil(found)) return found
        }
      }
      return null
    }

    const treeNode = findTreeNode(treeData, key)
    if (isNil(treeNode?.columnConfig)) {
      return
    }

    const { columnConfig, childIndex } = treeNode

    if (action === 'delete') {
      if (isNil(enrichedConfig)) return

      if (childIndex !== undefined) {
        const columns = [...(enrichedConfig.columnConfig?.columns ?? [])]
        const columnIndex = columns.findIndex(c => c === columnConfig)
        if (columnIndex === -1) return

        const updatedColumn = {
          ...columnConfig,
          attributes: {
            ...columnConfig.attributes,
            children: [...(columnConfig.attributes.children ?? [])]
          }
        }
        updatedColumn.attributes.children.splice(childIndex, 1)

        columns[columnIndex] = updatedColumn

        updateColumns(columns)
      } else {
        const columns = [...(enrichedConfig.columnConfig?.columns ?? [])]
        const columnIndex = columns.findIndex(c => c === columnConfig)
        if (columnIndex === -1) return

        columns.splice(columnIndex, 1)

        updateColumns(columns)
      }
    } else if (action === 'edit') {
      if (!isNonEmptyString(columnConfig.attributes.class)) {
        return
      }

      const columnIndex = enrichedConfig?.columnConfig?.columns?.findIndex(c => c === columnConfig) ?? -1
      if (columnIndex === -1) return

      setOperatorModalConfig({
        column: columnConfig,
        operatorId: columnConfig.attributes.class,
        columnIndex
      })
    }
  }

  const treeData = useMemo(() => buildTreeFromColumns(enrichedConfig, operatorRegistry, fieldDefinitionRegistry), [enrichedConfig, operatorRegistry, fieldDefinitionRegistry])

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

  return (
    <>
      <TreeElement
        blockNode
        defaultExpandedKeys={ allKeys }
        onActionsClick={ handleActionsClick }
        selectable={ false }
        showIcon
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
