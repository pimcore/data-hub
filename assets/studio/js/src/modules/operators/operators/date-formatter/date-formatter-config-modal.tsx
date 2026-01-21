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
import { Input, Form, Flex, Button, Modal, IconTextButton, FormKit } from '@pimcore/studio-ui-bundle/components'
import { type OperatorConfigModalProps, type ColumnConfig } from '../../dynamic-type-operator-abstract'
import { type DateFormatterAttributes } from './dynamic-type-operator-date-formatter'

export function DateFormatterConfigModal ({ config, onApply, onCancel }: OperatorConfigModalProps<DateFormatterAttributes>): React.JSX.Element {
  const { t } = useTranslation()
  const [form] = Form.useForm()

  useState(() => {
    form.setFieldsValue({
      label: config.attributes.label ?? 'DateFormatter',
      format: config.attributes.format ?? 'Y-m-d H:i:s'
    })
  })

  const handleApply = async (): Promise<void> => {
    const values = await form.validateFields()

    const updatedConfig: ColumnConfig<DateFormatterAttributes> = {
      ...config,
      attributes: {
        ...config.attributes,
        label: values.label,
        format: values.format
      }
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
            {t('data-hub.help')}
          </IconTextButton>

          <Flex gap="small">
            <Button onClick={ onCancel }>
              {t('cancel')}
            </Button>
            <Button
              onClick={ () => { void handleApply() } }
              type="primary"
            >
              {t('apply')}
            </Button>
          </Flex>
        </Flex>
      }
      onCancel={ onCancel }
      open
      size="M"
      title={ t('data-hub.operator.dateformatter.settings') }
    >
      <FormKit
        formProps={ {
          form
        } }
      >
        <Form.Item
          label={ t('data-hub.label') }
          name="label"
          rules={ [{ required: true, message: t('form.validation.required') }] }
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
      </FormKit>
    </Modal>
  )
}
