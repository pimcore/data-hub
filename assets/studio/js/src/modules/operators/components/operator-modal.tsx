/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useEffect } from 'react'
import { Modal, Form, FormKit } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type OperatorConfigModalProps } from '../dynamic-type-operator-abstract'
import { useOperatorModal } from '../hooks/use-operator-modal'
import type { FormInstance } from 'antd'

interface OperatorModalProps<T = any> extends OperatorConfigModalProps<T> {
  children: (context: { form: FormInstance }) => React.ReactNode
  initialValues?: Partial<T>
  size?: React.ComponentProps<typeof Modal>['size']
  footer?: (context: { handleApply: () => Promise<void>, onCancel: () => void }) => React.ReactNode
}

export function OperatorModal<T = any> ({
  children,
  initialValues,
  size,
  footer,
  ...props
}: OperatorModalProps<T>): React.JSX.Element {
  const { onCancel } = props
  const { t } = useTranslation()
  const { localizedName, getInitialValues, updateAttributes } = useOperatorModal(props)
  const [form] = Form.useForm()

  useEffect(() => {
    const initialData = getInitialValues(initialValues)
    form.setFieldsValue(initialData)
  }, [])

  const handleApply = async (): Promise<void> => {
    const values: Partial<T> = await form.validateFields()
    updateAttributes(values)
  }

  return (
    <Modal
      footer={ footer?.({ handleApply, onCancel }) }
      okText={ t('apply') }
      onCancel={ onCancel }
      onOk={ footer === undefined ? () => { void handleApply() } : undefined }
      open
      size={ size }
      title={ localizedName }
    >
      <FormKit formProps={ { form } }>
        {children({ form })}
      </FormKit>
    </Modal>
  )
}
