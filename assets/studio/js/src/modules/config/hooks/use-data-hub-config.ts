/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useCallback, useEffect } from 'react'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { isNil, isUndefined, has, isString } from 'lodash'
import {
  useBundleDataHubConfigAddMutation,
  useBundleDataHubConfigCloneMutation,
  useBundleDataHubConfigDeleteMutation,
  type BundleDataHubConfiguration,
  type BundleDataHubConfigCollectionApiResponse
} from '../config-api-slice-enhanced'
import { findConfigInTree } from '../utils/tree-helpers'
import { ApiError, trackError } from '@pimcore/studio-ui-bundle/modules/app'

interface UseDataHubConfigReturn {
  handleAdd: (adapterType: string, onSuccess?: (config: BundleDataHubConfiguration) => void) => void
  handleClone: (config: BundleDataHubConfiguration, onSuccess?: (config: BundleDataHubConfiguration) => void) => void
  handleDelete: (config: BundleDataHubConfiguration, onSuccess?: () => void) => void
}

interface UseDataHubConfigProps {
  refetch: () => Promise<{ data?: BundleDataHubConfigCollectionApiResponse }>
}

export const useDataHubConfig = ({ refetch }: UseDataHubConfigProps): UseDataHubConfigReturn => {
  const { t } = useTranslation()
  const modal = useFormModal()
  const [addConfig, { error: addError }] = useBundleDataHubConfigAddMutation()
  const [cloneConfig, { error: cloneError }] = useBundleDataHubConfigCloneMutation()
  const [deleteConfig, { error: deleteError }] = useBundleDataHubConfigDeleteMutation()

  const validateConfigName = async (_rule: any, value: string): Promise<void> => {
    if (!isString(value) || value.trim().length === 0) {
      throw new Error(t('data-hub.config.name-required'))
    }
    if (value.length < 3) {
      throw new Error(t('data-hub.config.name-min-length'))
    }
    if (value.length > 80) {
      throw new Error(t('data-hub.config.name-max-length'))
    }
    if (!/^[a-zA-Z0-9_-]+$/.test(value)) {
      throw new Error(t('data-hub.config.name-pattern'))
    }
    await Promise.resolve()
  }

  useEffect(() => {
    if (!isNil(addError)) {
      trackError(new ApiError(addError))
    }
  }, [addError])

  useEffect(() => {
    if (!isNil(cloneError)) {
      trackError(new ApiError(cloneError))
    }
  }, [cloneError])

  useEffect(() => {
    if (!isNil(deleteError)) {
      const apiError = new ApiError(deleteError)
      trackError(apiError)
    }
  }, [deleteError])

  const handleAdd = useCallback((
    adapterType: string,
    onSuccess?: (config: BundleDataHubConfiguration) => void
  ): void => {
    modal.input({
      label: t('data-hub.add.name'),
      rule: {
        required: true,
        validator: validateConfigName
      },
      onOk: async (value: string) => {
        const result = await addConfig({ name: value, type: adapterType })

        if (has(result, 'error')) {
          return
        }

        const { data: updatedData } = await refetch()

        if (!isUndefined(updatedData?.items)) {
          const addedConfig = findConfigInTree(updatedData.items, (item) =>
            !isUndefined(item.id) && item.id === value
          )

          if (!isUndefined(addedConfig) && !isNil(onSuccess)) {
            onSuccess(addedConfig)
          }
        }
      }
    })
  }, [addConfig, refetch, modal])

  const handleClone = useCallback((
    config: BundleDataHubConfiguration,
    onSuccess?: (config: BundleDataHubConfiguration) => void
  ): void => {
    if (isNil(config)) return

    modal.input({
      label: t('data-hub.clone.name'),
      rule: {
        required: true,
        validator: validateConfigName
      },
      onOk: async (value: string) => {
        const configId = String(config.id ?? '')
        const result = await cloneConfig({ name: value, originalName: configId })

        if (has(result, 'error')) {
          return
        }

        const { data: updatedData } = await refetch()

        if (!isUndefined(updatedData?.items)) {
          const clonedConfig = findConfigInTree(updatedData.items, (item) =>
            !isUndefined(item.id) && item.id === value
          )

          if (!isUndefined(clonedConfig) && !isNil(onSuccess)) {
            onSuccess(clonedConfig)
          }
        }
      }
    })
  }, [cloneConfig, refetch, modal])

  const handleDelete = useCallback((
    config: BundleDataHubConfiguration,
    onSuccess?: () => void
  ): void => {
    if (isNil(config)) return

    modal.confirm({
      title: t('delete'),
      content: t('data-hub.delete.confirm', { name: config.text }),
      onOk: async () => {
        const configId = String(config.id ?? '')
        const result = await deleteConfig({ name: configId })

        if (has(result, 'error')) {
          return
        }

        await refetch()

        if (!isNil(onSuccess)) {
          onSuccess()
        }
      }
    })
  }, [deleteConfig, refetch, modal])

  return {
    handleAdd,
    handleClone,
    handleDelete
  }
}
