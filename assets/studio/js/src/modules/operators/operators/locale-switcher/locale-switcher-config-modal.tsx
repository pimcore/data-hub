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
import { Input, Form, Flex, Button, Modal, Select, FormKit } from '@pimcore/studio-ui-bundle/components'
import { type OperatorConfigModalProps, type ColumnConfig } from '../../dynamic-type-operator-abstract'
import { useSettings } from '@pimcore/studio-ui-bundle/modules/app'
import { useLanguageLookup } from '@pimcore/studio-ui-bundle/modules/translations'
import { type LocaleSwitcherAttributes } from './dynamic-type-operator-locale-switcher'

export function LocaleSwitcherConfigModal ({ config, onApply, onCancel }: OperatorConfigModalProps<LocaleSwitcherAttributes>): React.JSX.Element {
  const { t } = useTranslation()
  const settings = useSettings()
  const { getDisplayName } = useLanguageLookup()
  const [form] = Form.useForm()

  useState(() => {
    form.setFieldsValue({
      label: config.attributes.label ?? 'Locale Switcher',
      locale: config.attributes.locale ?? ''
    })
  })

  const availableLanguages = (settings.validLanguages ?? []).map((locale: string) => ({
    value: locale,
    label: `${getDisplayName(locale)} [${locale}]`
  }))

  const handleApply = async (): Promise<void> => {
    const values = await form.validateFields()

    const updatedConfig: ColumnConfig<LocaleSwitcherAttributes> = {
      ...config,
      attributes: {
        ...config.attributes,
        label: values.label,
        locale: values.locale
      }
    }

    onApply(updatedConfig)
  }

  return (
    <Modal
      footer={
        <Flex justify="flex-end">
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
      title={ t('data-hub.operator.localeswitcher.settings') }
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
          label={ t('data-hub.locale') }
          name="locale"
          rules={ [{ required: true, message: t('form.validation.required') }] }
        >
          <Select
            options={ availableLanguages }
            placeholder={ t('data-hub.locale.select') }
          />
        </Form.Item>
      </FormKit>
    </Modal>
  )
}
