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
import { isNil } from 'lodash'
import { useStyles } from './schema-fields-modal.styles'
import { useSidebarEntries } from './hooks/use-sidebar-entries'

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
  const { getByName } = useClassDefinitions()

  const classDefinition = getByName(className)

  const sidebarEntries = useSidebarEntries({
    classId: classDefinition?.id ?? '',
    enabled: open && !isNil(classDefinition),
    operatorRegistryServiceId,
    gridContainerClassName: styles.gridContainer
  })

  const [localEntityConfig, setLocalEntityConfig] = useState<QueryEntityConfig | undefined>(undefined)

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
        <Content
          padded
          padding={ { right: 'medium' } }
        >
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
