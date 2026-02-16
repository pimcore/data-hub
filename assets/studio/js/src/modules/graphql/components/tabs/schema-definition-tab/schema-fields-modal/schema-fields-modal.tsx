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
import { Modal, Flex, Button, Form, Content, ContentLayout, Sidebar, SidebarProvider, Title } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { useClassDefinitions } from '@pimcore/studio-ui-bundle/modules/data-object'
import { AvailableFieldsTree } from './available-fields-tree'
import { type QueryEntityConfig } from './types'
import { isNil, cloneDeep } from 'lodash'
import { useStyles } from './schema-fields-modal.styles'
import { useSidebarEntries } from './hooks/use-sidebar-entries'
import { AddAllDefinitionsButton } from './components/add-all-definitions-button'

interface SchemaFieldsModalProps {
  open: boolean
  className: string
  operatorRegistryServiceId: string
  type?: 'query' | 'mutation'
  disabled?: boolean
  onCancel: () => void
  onApply: () => void
}

export const SchemaFieldsModal = ({
  open,
  className,
  operatorRegistryServiceId,
  type = 'query',
  disabled = false,
  onCancel,
  onApply
}: SchemaFieldsModalProps): React.JSX.Element => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const form = Form.useFormInstance()
  const { getByName } = useClassDefinitions()

  const classDefinition = getByName(className)
  const classId = classDefinition?.id ?? ''
  const enabled = open && !isNil(classDefinition)

  const sidebarEntries = useSidebarEntries({
    classId,
    enabled,
    operatorRegistryServiceId,
    gridContainerClassName: styles.gridContainer
  })

  const [localEntityConfig, setLocalEntityConfig] = useState<QueryEntityConfig | undefined>(undefined)

  useEffect(() => {
    if (open) {
      const entities = form.getFieldValue(['schema', type]) ?? []
      const entity: QueryEntityConfig | undefined = entities.find((e: any) => e.entity === className)
      setLocalEntityConfig(isNil(entity) ? undefined : cloneDeep(entity))
    }
  }, [open, form, className, type])

  const handleApply = (): void => {
    const entities = form.getFieldValue(['schema', type]) ?? []
    const entityIndex = entities.findIndex((e: any) => e.entity === className)

    if (entityIndex !== -1 && !isNil(localEntityConfig)) {
      const updatedEntities = [...entities]
      updatedEntities[entityIndex] = localEntityConfig

      form.setFieldValue(['schema', type], updatedEntities, { triggerChange: true })
    }

    onApply()
  }

  return (
    <Modal
      footer={ disabled
        ? null
        : (
          <Flex
            gap="small"
            justify="space-between"
          >
            <AddAllDefinitionsButton
              classId={ classId }
              enabled={ enabled }
              entityConfig={ localEntityConfig }
              onEntityConfigChange={ setLocalEntityConfig }
            />
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
        renderSidebar={ disabled
          ? undefined
          : (
            <SidebarProvider initialActiveTab={ sidebarEntries[0]?.key }>
              <Sidebar
                entries={ sidebarEntries }
                sizing="large"
              />
            </SidebarProvider>
            ) }
      >
        <Content
          padded
          padding={ { right: 'medium' } }
        >
          <Title level={ 3 }>{t('data-hub.schema.available-fields')}</Title>
          <AvailableFieldsTree
            disabled={ disabled }
            entityConfig={ localEntityConfig }
            onEntityConfigChange={ setLocalEntityConfig }
            operatorRegistryServiceId={ operatorRegistryServiceId }
          />
        </Content>
      </ContentLayout>
    </Modal>
  )
}
