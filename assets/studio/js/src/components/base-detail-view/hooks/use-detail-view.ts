/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useEffect, useState, useMemo, useRef } from 'react'
import { Form, useMessage, type formInstanceType } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { ApiError, trackError, isApiErrorData } from '@pimcore/studio-ui-bundle/modules/app'
import { isNil } from 'lodash'

export interface UseDetailViewProps<TFormValues, TBackendConfig> {
  configName: string
  configData: TBackendConfig | undefined
  modificationDate: number | undefined
  isLoading: boolean
  requestId: string | undefined
  transformToForm: (backendConfig: TBackendConfig, configName: string) => TFormValues
  transformToBackend: (formValues: TFormValues, existingConfig: TBackendConfig) => TBackendConfig
  onSave: (updatedConfig: TBackendConfig, modificationDate: number) => Promise<{ modificationDate?: number }>
  onChange: (isDirty: boolean) => void
  successMessageKey?: string
}

export interface UseDetailViewReturn<TFormValues> {
  form: formInstanceType
  isDirty: boolean
  initialValues: TFormValues
  handleSave: () => void
  handleValuesChange: () => void
}

export function useDetailView<TFormValues extends Record<string, any>, TBackendConfig extends Record<string, any>> ({
  configName,
  configData,
  modificationDate,
  isLoading,
  requestId,
  transformToForm,
  transformToBackend,
  onSave,
  onChange,
  successMessageKey = 'save-success'
}: UseDetailViewProps<TFormValues, TBackendConfig>): UseDetailViewReturn<TFormValues> {
  const [form] = Form.useForm()
  const { t } = useTranslation()
  const messageApi = useMessage()
  const [isDirty, setIsDirty] = useState(false)
  const modificationDateRef = useRef<number>(0)
  const isSavingRef = useRef<boolean>(false)

  const initialValues = useMemo<TFormValues>(() => {
    return transformToForm((configData ?? {}) as TBackendConfig, configName)
  }, [configData, configName])

  useEffect(() => {
    if (isLoading || isNil(configData)) return

    form.setFieldsValue(initialValues)
    setIsDirty(false)
    onChange(false)

    if (!isSavingRef.current) {
      modificationDateRef.current = modificationDate ?? 0
    }
  }, [requestId, initialValues, modificationDate, isLoading])

  const handleValuesChange = (): void => {
    setIsDirty(true)
    onChange(true)
  }

  const handleSave = (): void => {
    if (isSavingRef.current) {
      return
    }

    form.validateFields().then(async (values) => {
      isSavingRef.current = true

      try {
        const mergedValues: TFormValues = {
          ...initialValues,
          ...values as TFormValues
        }

        const updatedConfig = transformToBackend(mergedValues, (configData ?? {}) as TBackendConfig)
        const response = await onSave(updatedConfig, modificationDateRef.current)

        if (!isNil(response?.modificationDate)) {
          modificationDateRef.current = response.modificationDate
        }

        setIsDirty(false)
        onChange(false)
        void messageApi.success(t(successMessageKey))
      } catch (error) {
        if (isApiErrorData(error)) {
          trackError(new ApiError(error))
        }
      } finally {
        isSavingRef.current = false
      }
    }).catch((error) => {
      console.error('Validation failed:', error)
      void messageApi.error(t('data-hub.save-validation-error'))
    })
  }

  return {
    form,
    isDirty,
    initialValues,
    handleSave,
    handleValuesChange
  }
}
