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
import { Modal, Flex, Button, Form, Content, ConfigLayout, Title } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { AvailableFieldsTree } from './available-fields-tree'
import { type QueryEntityConfig } from './types'
import { isNil } from 'lodash'

interface SchemaFieldsModalProps {
  open: boolean
  entityName: string
  onCancel: () => void
  onApply: () => void
  onFormChange?: () => void
}

export const SchemaFieldsModal = ({
  open,
  entityName,
  onCancel,
  onApply,
  onFormChange
}: SchemaFieldsModalProps): React.JSX.Element => {
  const { t } = useTranslation()
  const form = Form.useFormInstance()

  const [localEntityConfig, setLocalEntityConfig] = useState<QueryEntityConfig | undefined>(undefined)

  useEffect(() => {
    if (open) {
      const queryEntities = form.getFieldValue(['schema', 'query']) ?? []
      const entity: QueryEntityConfig | undefined = queryEntities.find((e: any) => e.entity === entityName)
      setLocalEntityConfig(!isNil(entity) ? JSON.parse(JSON.stringify(entity)) as QueryEntityConfig : undefined)
    }
  }, [open, form, entityName])

  const handleApply = (): void => {
    const queryEntities = form.getFieldValue(['schema', 'query']) ?? []
    const entityIndex = queryEntities.findIndex((e: any) => e.entity === entityName)

    if (entityIndex !== -1 && !isNil(localEntityConfig)) {
      const updatedEntities = [...queryEntities]
      updatedEntities[entityIndex] = localEntityConfig

      form.setFieldValue(['schema', 'query'], updatedEntities)

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
      onCancel={ onCancel }
      open={ open }
      title={ t('data-hub.schema.modal-title', { entity: entityName }) }
      size="XL"
    >
      <div style={ { height: '600px' } }>
        <ConfigLayout
          leftItem={ {
            minSize: 200,
            size: 400,
            maxSize: 600,
            children: (
              <Content padded>
                <Title>{t('data-hub.schema.class-attributes')}</Title>
                <div style={ { color: '#999' } }>
                  {t('data-hub.schema.coming-soon')}
                </div>
              </Content>
            )
          } }
          rightItem={ {
            children: (
              <Content padded>
                <Title>{t('data-hub.schema.available-fields')}</Title>
                <AvailableFieldsTree
                  entityConfig={ localEntityConfig }
                  entityName={ entityName }
                  onEntityConfigChange={ setLocalEntityConfig }
                />
              </Content>
            )
          } }
          resizeAble
        />
      </div>
    </Modal>
  )
}
