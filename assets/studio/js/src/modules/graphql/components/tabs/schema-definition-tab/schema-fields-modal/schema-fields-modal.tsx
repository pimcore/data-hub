/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useState, useEffect } from 'react'
import { Modal, Flex, Button, Form, Content, ConfigLayout, Icon, Tabs, TreeElement, Panel, Draggable } from '@pimcore/studio-ui-bundle/components'
import { useTranslation, useInjection, serviceIds } from '@pimcore/studio-ui-bundle/app'
import { AvailableFieldsTree } from './available-fields-tree'
import { type QueryEntityConfig } from './types'
import { isNil } from 'lodash'
import { type DynamicTypeOperatorRegistry } from '../../../../../../modules/operators/dynamic-type-operator-registry'
import { useClassDefinitionGetLayoutByIdQuery } from '@pimcore/studio-ui-bundle/api/class-definition'
import { reduce, buildTree } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { type DynamicTypeFieldDefinitionRegistry } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { useStyles } from './schema-fields-modal.styles'

interface SchemaFieldsModalProps {
  open: boolean
  entityName: string
  operatorRegistryServiceId: string
  type?: 'query' | 'mutation'
  onCancel: () => void
  onApply: () => void
  onFormChange?: () => void
}

export const SchemaFieldsModal = ({
  open,
  entityName,
  operatorRegistryServiceId,
  type = 'query',
  onCancel,
  onApply,
  onFormChange
}: SchemaFieldsModalProps): React.JSX.Element => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const form = Form.useFormInstance()
  const operatorRegistry = useInjection<DynamicTypeOperatorRegistry>(operatorRegistryServiceId)
  const { data: classLayout } = useClassDefinitionGetLayoutByIdQuery(
    { id: 'EV' },
    { skip: !open }
  )
  const fieldDefinitionRegistry = useInjection<DynamicTypeFieldDefinitionRegistry>(serviceIds['DynamicTypes/FieldDefinitionRegistry'])

  const [localEntityConfig, setLocalEntityConfig] = useState<QueryEntityConfig | undefined>(undefined)

  // Helper function to collect all keys from tree
  const collectAllKeys = (nodes: any[]): string[] => {
    const keys: string[] = []
    const traverse = (items: any[]): void => {
      items.forEach(item => {
        if (!isNil(item.key)) keys.push(String(item.key))
        if (!isNil(item.children) && Array.isArray(item.children)) {
          traverse(item.children as any[])
        }
      })
    }
    traverse(nodes)
    return keys
  }

  // Build class attributes tree
  const classAttributesTree = React.useMemo(() => {
    let treeData: any[] = []
    try {
      if (isNil(classLayout)) {
        treeData = []
      } else {
        // classLayout IS the layout object itself, not wrapped
        const reduced = reduce({ layout: classLayout })

        if (reduced?.structure === undefined) {
          treeData = []
        } else {
          const { structure, fieldDefinitions } = reduced

          const tree = buildTree({
            structure,
            fieldDefinitions,
            itemCallback: ({ fieldDefinition, initialTreeItem }) => {
              // Get the icon props from the field definition registry
              const dynType = fieldDefinitionRegistry.hasDynamicType(fieldDefinition.fieldtype)
                ? fieldDefinitionRegistry.getDynamicType(fieldDefinition.fieldtype)
                : undefined
              
              // Destructure to exclude the icon (React element) from initialTreeItem
              // The icon from buildTree is a React element which can cause DnD issues
              const { icon: _icon, ...restTreeItem } = initialTreeItem
              
              return {
                ...restTreeItem,
                className: 'ant-tree-node--has-drag-and-drop',
                // Re-add the icon from initialTreeItem (it's already correct for display)
                icon: initialTreeItem.icon,
                // Add dataType from fieldDefinition for use in drag data
                dataType: fieldDefinition.fieldtype,
                // Add serializable icon props for drag overlay (NOT React element)
                iconProps: dynType !== undefined ? dynType.getIcon() : { value: 'field' }
              }
            }
          })

          // buildTree returns a single root node object, not an array
          // We want to show the children of the root node
          if (!isNil(tree?.children) && Array.isArray(tree.children)) {
            treeData = tree.children
          } else {
            treeData = []
          }
        }
      }
    } catch (error) {
      console.error('Error building class attributes tree:', error)
      treeData = []
    }
    return treeData
  }, [classLayout, fieldDefinitionRegistry])

  useEffect(() => {
    if (open) {
      const entities = form.getFieldValue(['schema', type]) ?? []
      const entity: QueryEntityConfig | undefined = entities.find((e: any) => e.entity === entityName)
      setLocalEntityConfig(!isNil(entity) ? JSON.parse(JSON.stringify(entity)) as QueryEntityConfig : undefined)
    }
  }, [open, form, entityName, type])

  const handleApply = (): void => {
    const entities = form.getFieldValue(['schema', type]) ?? []
    const entityIndex = entities.findIndex((e: any) => e.entity === entityName)

    if (entityIndex !== -1 && !isNil(localEntityConfig)) {
      const updatedEntities = [...entities]
      updatedEntities[entityIndex] = localEntityConfig

      form.setFieldValue(['schema', type], updatedEntities)

      if (!isNil(onFormChange)) {
        onFormChange()
      }
    }

    onApply()
  }

  // Create titleRender for class attributes
  const classAttributesTitleRender = React.useMemo(() => {
    const ClassAttributeTitleRenderer = (node: any, initialComponent: React.ReactNode): React.JSX.Element => (
      <Draggable
        info={ {
          type: 'class-attribute',
          data: {
            key: String(node.key),
            title: String(node.title),
            dataType: String(node.dataType ?? 'text')
          },
          icon: node.iconProps ?? { value: 'field' },
          title: String(node.title)
        } }
      >
        {initialComponent}
      </Draggable>
    )
    ClassAttributeTitleRenderer.displayName = 'ClassAttributeTitleRenderer'
    return ClassAttributeTitleRenderer
  }, [])

  // Create titleRender for operators
  const operatorsTitleRender = React.useMemo(() => {
    const OperatorTitleRenderer = (node: any, initialComponent: React.ReactNode): React.JSX.Element => (
      <Draggable
        info={ {
          type: 'operator',
          data: {
            key: String(node.key),
            title: String(node.title),
            operatorId: String(node.key.toString().split('-').pop())
          },
          icon: { value: 'function' },
          title: String(node.title)
        } }
      >
        {initialComponent}
      </Draggable>
    )
    OperatorTitleRenderer.displayName = 'OperatorTitleRenderer'
    return OperatorTitleRenderer
  }, [])

  // Build tab items for class attributes and operators
  const tabItems = React.useMemo(() => {
    const items: Array<{ key: string, label: string, children: React.JSX.Element }> = []

    // Add class attributes tab
    items.push({
      key: 'class-attributes',
      label: t('data-hub.schema.class-attributes'),
      children: (

        <TreeElement
          defaultExpandedKeys={ collectAllKeys(classAttributesTree) }
          draggable={ false }
          selectable={ false }
          showIcon
          titleRender={ classAttributesTitleRender }
          treeData={ classAttributesTree }
        />
      )
    })

    // Add operator tabs
    const operators = operatorRegistry.getDynamicTypes()
    const groups = new Map<string, Map<string | undefined, Array<{ id: string, icon: any, name: string }>>>()

    // Organize operators by group and subgroup
    operators.forEach(operator => {
      const groupKey = operator.getGroupTranslationKey()
      const subGroupKey = operator.getSubGroupKey()

      if (!groups.has(groupKey)) {
        groups.set(groupKey, new Map())
      }

      const groupMap = groups.get(groupKey)!
      if (!groupMap.has(subGroupKey)) {
        groupMap.set(subGroupKey, [])
      }

      groupMap.get(subGroupKey)?.push({
        id: operator.id,
        icon: operator.getIcon(),
        name: t(operator.getNameTranslationKey())
      })
    })

    Array.from(groups.entries()).forEach(([groupKey, subGroups]) => {
      const treeData: any[] = []

      // Build tree with subgroups and operators
      Array.from(subGroups.entries()).forEach(([subGroupKey, operators]) => {
        const sortedOperators = operators.sort((a, b) => a.name.localeCompare(b.name))

        if (subGroupKey === undefined) {
          // No subgroup - add operators directly
          sortedOperators.forEach(operator => {
            treeData.push({
              key: `${groupKey}-${operator.id}`,
              title: operator.name,
              icon: <Icon { ...operator.icon } />,
              isLeaf: true
            })
          })
        } else {
          // Has subgroup - create folder
          treeData.push({
            key: `${groupKey}-${subGroupKey}`,
            title: t(subGroupKey),
            icon: <Icon value="folder" />,
            children: sortedOperators.map(operator => ({
              key: `${groupKey}-${subGroupKey}-${operator.id}`,
              title: operator.name,
              icon: <Icon { ...operator.icon } />,
              className: 'ant-tree-node--has-drag-and-drop',
              isLeaf: true
            }))
          })
        }
      })

      items.push({
        key: groupKey,
        label: t(groupKey),
        children: (
          <Content padded>
            <TreeElement

              defaultExpandedKeys={ collectAllKeys(treeData) }
              draggable={ false }
              selectable={ false }
              showIcon
              titleRender={ operatorsTitleRender }
              treeData={ treeData }
            />
          </Content>
        )
      })
    })

    return items
  }, [operatorRegistry, t, classAttributesTree])

  return (
    <Modal
      footer={ (
        <Flex
          gap="small"
          justify="flex-end"
        >
          <Button
            onClick={ handleApply }
            type="primary"
          >
            {t('button.apply')}
          </Button>
        </Flex>
      ) }
      onCancel={ onCancel }
      open={ open }
      size="XL"
      title={ t(`data-hub.schema.${type}-modal-title`, { entity: entityName }) }
    >
      <div style={ { height: '600px' } }>
        <ConfigLayout
          leftItem={ {
            minSize: 200,
            size: 400,
            maxSize: 600,
            children: (
              <Tabs
                className={ styles.tabs }
                defaultActiveKey="class-attributes"
                hasStickyHeader
                items={ tabItems }
                size="small"
                style={ { height: '100%' } }
                tabPosition="left"
              />
            )
          } }
          resizeAble
          rightItem={ {
            children: (
              <Content padded>
                <Panel
                  theme="default"
                  title={ t('data-hub.schema.available-fields') }
                >
                  <AvailableFieldsTree
                    entityConfig={ localEntityConfig }
                    entityName={ entityName }
                    onEntityConfigChange={ setLocalEntityConfig }
                    operatorRegistryServiceId={ operatorRegistryServiceId }
                  />
                </Panel>
              </Content>
            )
          } }
        />
      </div>
    </Modal>
  )
}
