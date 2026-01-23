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
import { Modal, Flex, Button, Form, Content, ConfigLayout, Icon, Tabs, TreeElement, Panel, Draggable, ContentLayout, Toolbar } from '@pimcore/studio-ui-bundle/components'
import { useTranslation, useInjection } from '@pimcore/studio-ui-bundle/app'
import { useClassDefinitions } from '@pimcore/studio-ui-bundle/modules/data-object'
import { AvailableFieldsTree } from './available-fields-tree'
import { type QueryEntityConfig } from './types'
import { isNil } from 'lodash'
import { type DynamicTypeOperatorRegistry } from '../../../../../../modules/operators/dynamic-type-operator-registry'
import { useStyles } from './schema-fields-modal.styles'
import { useClassAttributesTree } from './hooks/use-class-attributes-tree'

interface SchemaFieldsModalProps {
  open: boolean
  className: string
  operatorRegistryServiceId: string
  type?: 'query' | 'mutation'
  onCancel: () => void
  onApply: () => void
  onFormChange?: () => void
}

export const SchemaFieldsModal = ({
  open,
  className,
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
  const { getByName } = useClassDefinitions()

  const classDefinition = getByName(className)
  const { classAttributesTree, isLoading } = useClassAttributesTree({
    classId: classDefinition?.id ?? '',
    enabled: open && classDefinition !== undefined
  })

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

  useEffect(() => {
    if (open) {
      const entities = form.getFieldValue(['schema', type]) ?? []
      const entity: QueryEntityConfig | undefined = entities.find((e: any) => e.entity === className)
      setLocalEntityConfig(!isNil(entity) ? JSON.parse(JSON.stringify(entity)) as QueryEntityConfig : undefined)
    }
  }, [open, form, className, type])

  const handleApply = (): void => {
    const entities = form.getFieldValue(['schema', type]) ?? []
    const entityIndex = entities.findIndex((e: any) => e.entity === className)

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
    const ClassAttributeTitleRenderer = (node: any, initialComponent: React.ReactNode): React.JSX.Element => {
      const isLeaf = node.isLeaf === true || (isNil(node.children) || node.children.length === 0)

      if (!isLeaf) {
        return <>{initialComponent}</>
      }

      return (
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
    }
    ClassAttributeTitleRenderer.displayName = 'ClassAttributeTitleRenderer'
    return ClassAttributeTitleRenderer
  }, [])

  // Create titleRender for operators
  const operatorsTitleRender = React.useMemo(() => {
    const OperatorTitleRenderer = (node: any, initialComponent: React.ReactNode): React.JSX.Element => {
      const isLeaf = node.isLeaf === true || (isNil(node.children) || node.children.length === 0)

      if (!isLeaf) {
        return <>{initialComponent}</>
      }

      return (
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
    }
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
      icon: <Icon value="object-data" />,
      children: (
        <Content
          loading={ isLoading }
          padded
        >
          <TreeElement
            defaultExpandedKeys={ collectAllKeys(classAttributesTree) }
            draggable={ false }
            selectable={ false }
            showIcon
            titleRender={ classAttributesTitleRender }
            treeData={ classAttributesTree }
          />
        </Content>
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
  }, [classAttributesTree, isLoading])

  return (
    <Modal
      footer={ null }
      key={ `${className}-${type}` }
      onCancel={ onCancel }
      open={ open }
      size="XXL"
      title={ t(`data-hub.schema.${type}-modal-title`, { entity: className }) }
    >
      <ContentLayout
        className={ styles.contentLayout }
        renderToolbar={ (
          <Toolbar
              padding={ { x: 'none' } }
              position="bottom"
              theme="secondary"
            >
              <Flex
                gap="small"
                justify="flex-end"
                style={ { width: '100%' } }
              >
                <Button
                  onClick={ handleApply }
                  type="primary"
                >
                  {t('button.apply')}
                </Button>
              </Flex>
            </Toolbar>
          ) }
        >
          <Content style={ { height: '100%' } }>
            <ConfigLayout
            withToolbar={ false }
              leftItem={ {
                minSize: 200,
                size: 400,
                maxSize: 600,
                children: (
                  <Tabs
                    className={ styles.tabs }
                    defaultActiveKey="class-attributes"
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
                        onEntityConfigChange={ setLocalEntityConfig }
                        operatorRegistryServiceId={ operatorRegistryServiceId }
                      />
                    </Panel>
                  </Content>
                )
              } }
            />
          </Content>
        </ContentLayout>
    </Modal>
  )
}
