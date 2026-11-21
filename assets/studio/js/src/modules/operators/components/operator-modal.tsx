/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useCallback, useEffect } from 'react'
import { Modal, Form, FormKit } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type OperatorConfigModalProps } from '../dynamic-type-operator-abstract'
import { useOperatorModal } from '../hooks/use-operator-modal'
import type { FormInstance } from 'antd'

interface OperatorModalProps<T = any> extends OperatorConfigModalProps<T> {
  children: (context: { form: FormInstance }) => React.ReactNode
  initialValues?: Partial<T>
  size?: React.ComponentProps<typeof Modal>['size']
  footer?: (context: { handleApply: () => Promise<void>, onCancel: () => void, disabled: boolean }) => React.ReactNode
}

export const OperatorModal = <T = any>({
  children,
  initialValues,
  size,
  footer,
  disabled = false,
  ...props
}: OperatorModalProps<T>): React.JSX.Element => {
  const { onCancel } = props
  const { t } = useTranslation()
  const { localizedName, getInitialValues, updateAttributes } = useOperatorModal(props)
  const [form] = Form.useForm()

  useEffect(() => {
    const initialData = getInitialValues(initialValues)
    form.setFieldsValue(initialData as Partial<any>)
  }, [])

  const handleApply = useCallback(async (): Promise<void> => {
    if (disabled) return
    const values: Partial<T> = await form.validateFields()
    updateAttributes(values)
  }, [disabled, form, updateAttributes])

  useEffect(() => {
    if (disabled) return

    const handleKeyDown = (e: KeyboardEvent): void => {
      if (e.key === 'Enter') {
        void handleApply()
      }
    }

    document.addEventListener('keydown', handleKeyDown)
    return () => { document.removeEventListener('keydown', handleKeyDown) }
  }, [disabled, handleApply])

  return (
    <Modal
      footer={ footer?.({ handleApply, onCancel, disabled }) ?? (disabled ? null : undefined) }
      okText={ t('apply') }
      onCancel={ onCancel }
      onOk={ footer === undefined && !disabled ? () => { void handleApply() } : undefined }
      open
      size={ size }
      title={ localizedName }
    >
      <FormKit formProps={ { form, disabled } }>
        {children({ form })}
      </FormKit>
    </Modal>
  )
}
