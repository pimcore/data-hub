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
import { Modal, Flex, Button, Form, Content, ConfigLayout, Icon, Tabs, TreeElement, Panel, Draggable, ContentLayout, Toolbar, Sidebar, SidebarProvider, Title, SidebarTitle, Box } from '@pimcore/studio-ui-bundle/components'
import { useTranslation, useInjection } from '@pimcore/studio-ui-bundle/app'
import { useClassDefinitions } from '@pimcore/studio-ui-bundle/modules/data-object'
import { AvailableFieldsTree } from './available-fields-tree'
import { type QueryEntityConfig } from './types'
import { isNil } from 'lodash'
import { type DynamicTypeOperatorRegistry } from '../../../../../../modules/operators/dynamic-type-operator-registry'
import { useStyles } from './schema-fields-modal.styles'
import { useOperatorButtonStyles } from './operator-button.styles'
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
  const { styles: operatorStyles } = useOperatorButtonStyles()
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

  // Build sidebar entries for class attributes and operator groups
  const sidebarEntries = React.useMemo(() => {
    const entries: Array<{ key: string, icon: React.JSX.Element, tooltip: string, component: React.JSX.Element }> = []

    // Add class attributes entry
    entries.push({
      key: 'class-attributes',
      icon: <Icon value="data-object" />,
      tooltip: t('data-hub.schema.class-attributes'),
      component: (
        <Content loading={ isLoading }>
          <SidebarTitle withBorder>
            {t('data-hub.schema.class-attributes')}
          </SidebarTitle>

          <Box padding={ { x: 'extra-small', bottom: 'small' } }>
            <TreeElement
              defaultExpandedKeys={ collectAllKeys(classAttributesTree) }
              draggable={ false }
              selectable={ false }
              showIcon
              titleRender={ classAttributesTitleRender }
              treeData={ classAttributesTree }
            />
          </Box>
        </Content>
      )
    })

    // Add operator groups
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
      entries.push({
        key: groupKey,
        icon: <Icon value="data-object" />,
        tooltip: t(groupKey),
        component: (
          <Content>
            <SidebarTitle withBorder>
              {t(groupKey)}
            </SidebarTitle>

            <Box>
              {Array.from(subGroups.entries()).map(([subGroupKey, operators]) => {
                const sortedOperators = operators.sort((a, b) => a.name.localeCompare(b.name))

                if (subGroupKey === undefined) {
                  // No subgroup - render operators directly in grid
                  return (
                    <Box
                      className={ operatorStyles.gridContainer }
                      key={ `${groupKey}-direct` }
                      padding={ { x: 'extra-small', bottom: 'small' } }
                    >
                      {sortedOperators.map(operator => (
                        <Draggable
                          info={ {
                            type: 'operator',
                            data: {
                              key: `${groupKey}-${operator.id}`,
                              title: operator.name,
                              operatorId: operator.id
                            },
                            icon: operator.icon,
                            title: operator.name
                          } }
                          key={ `${groupKey}-${operator.id}` }
                        >
                          <Button
                            className={ operatorStyles.operatorButton }
                            type="default"
                          >
                            <Flex
                              align="center"
                              justify="center"
                              vertical
                            >
                              <Icon
                                { ...operator.icon }
                                className={ operatorStyles.operatorIcon }
                                options={ { width: 24, height: 24 } }
                              />
                              <span className={ operatorStyles.operatorName }>
                                {operator.name}
                              </span>
                            </Flex>
                          </Button>
                        </Draggable>
                      ))}
                    </Box>
                  )
                } else {
                  // Has subgroup - use collapsible panel
                  return (
                    <Panel
                      border={ false }
                      collapsed={ false }
                      collapsible
                      contentPadding="extra-small"
                      key={ `${groupKey}-${subGroupKey}` }
                      theme="card-with-highlight"
                      title={ t(subGroupKey) }
                    >
                      <Box className={ operatorStyles.gridContainer }>
                        {sortedOperators.map(operator => (
                          <Draggable
                            info={ {
                              type: 'operator',
                              data: {
                                key: `${groupKey}-${subGroupKey}-${operator.id}`,
                                title: operator.name,
                                operatorId: operator.id
                              },
                              icon: operator.icon,
                              title: operator.name
                            } }
                            key={ `${groupKey}-${subGroupKey}-${operator.id}` }
                          >
                            <Button
                              className={ operatorStyles.operatorButton }
                              type="default"
                            >
                              <Flex
                                align="center"
                                justify="center"
                                vertical
                              >
                                <Icon
                                  { ...operator.icon }
                                  className={ operatorStyles.operatorIcon }
                                  options={ { width: 24, height: 24 } }
                                />
                                <span className={ operatorStyles.operatorName }>
                                  {operator.name}
                                </span>
                              </Flex>
                            </Button>
                          </Draggable>
                        ))}
                      </Box>
                    </Panel>
                  )
                }
              })}
            </Box>
          </Content>
        )
      })
    })

    return entries
  }, [operatorRegistry, t, operatorsTitleRender, collectAllKeys, classAttributesTree, isLoading, classAttributesTitleRender])

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
      key={ `${className}-${type}` }
      onCancel={ onCancel }
      open={ open }
      size="XL"
      title={ t(`data-hub.schema.${type}-modal-title`, { entity: className }) }
    >
      <ContentLayout
        className={ styles.contentLayout }
        renderSidebar={ (
          <SidebarProvider initialActiveTab={ sidebarEntries[0]?.key }>
            <Sidebar
              entries={ sidebarEntries }
              sizing="large"
            />
          </SidebarProvider>
        ) }
        >
          <Content>
            <Title level={ 3 }>{t('data-hub.schema.available-fields')}</Title>
            <AvailableFieldsTree
              entityConfig={ localEntityConfig }
              onEntityConfigChange={ setLocalEntityConfig }
              operatorRegistryServiceId={ operatorRegistryServiceId }
            />
          </Content>
        </ContentLayout>
    </Modal>
  )
}
