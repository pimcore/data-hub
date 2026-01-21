/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useState } from 'react'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { Input, Form, Flex, Button, Modal, IconTextButton } from '@pimcore/studio-ui-bundle/components'
import { type OperatorConfigModalProps } from '../../dynamic-type-operator-abstract'
import type { ColumnConfig } from '../../../components/tabs/schema-definition-tab/schema-fields-modal/types'

export function DateFormatterConfigModal ({ config, onApply, onCancel }: OperatorConfigModalProps): React.JSX.Element {
  const { t } = useTranslation()
  const [form] = Form.useForm()

  useState(() => {
    form.setFieldsValue({
      label: config.attributes.label ?? 'DateFormatter',
      format: (config.attributes as any).format ?? 'Y-m-d H:i:s'
    })
  })

  const handleApply = async (): Promise<void> => {
    const values = await form.validateFields()

    const updatedConfig: ColumnConfig = {
      ...config,
      attributes: {
        ...config.attributes,
        label: values.label,
        format: values.format
      } as any
    }

    onApply(updatedConfig)
  }

  const openHelp = (): void => {
    window.open('https://www.php.net/manual/en/function.date.php', '_blank', 'noopener,noreferrer')
  }

  return (
    <Modal
      footer={
        <Flex justify="space-between">
          <IconTextButton
            icon={ { value: 'help-circle' } }
            onClick={ openHelp }
            type="default"
          >
            {t('data-hub.operator.help')}
          </IconTextButton>

          <Flex gap="small">
            <Button onClick={ onCancel }>
              {t('data-hub.operator.cancel')}
            </Button>
            <Button
              onClick={ () => { void handleApply() } }
              type="primary"
            >
              {t('data-hub.operator.apply')}
            </Button>
          </Flex>
        </Flex>
      }
      onCancel={ onCancel }
      open
      title={ t('data-hub.operator.dateformatter.settings') }
      width={ 500 }
    >
      <Form
        form={ form }
        layout="vertical"
      >
        <Form.Item
          label={ t('data-hub.operator.label') }
          name="label"
          rules={ [{ required: true, message: t('data-hub.operator.label.required') }] }
        >
          <Input maxLength={ 255 } />
        </Form.Item>

        <Form.Item
          label={ t('data-hub.operator.dateformatter.format') }
          name="format"
          rules={ [{ required: true, message: t('data-hub.operator.dateformatter.format.required') }] }
        >
          <Input maxLength={ 255 } />
        </Form.Item>
      </Form>
    </Modal>
  )
}
