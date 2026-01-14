/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useCallback } from 'react'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import {
  useBundleDataHubConfigAddMutation,
  useBundleDataHubConfigCloneMutation,
  useBundleDataHubConfigDeleteMutation,
  type BundleDataHubConfiguration,
  type BundleDataHubConfigCollectionApiResponse
} from '../config-api-slice-enhanced'
import { isNil, isUndefined } from 'lodash'
import { findConfigInTree } from '../utils/tree-helpers'

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
  const [addConfig] = useBundleDataHubConfigAddMutation()
  const [cloneConfig] = useBundleDataHubConfigCloneMutation()
  const [deleteConfig] = useBundleDataHubConfigDeleteMutation()

  const handleAdd = useCallback((
    adapterType: string,
    onSuccess?: (config: BundleDataHubConfiguration) => void
  ): void => {
    modal.input({
      label: t('data-hub.add.name'),
      onOk: async (value: string) => {
        await addConfig({ name: value, type: adapterType })
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
  }, [addConfig, refetch, modal, t])

  const handleClone = useCallback((
    config: BundleDataHubConfiguration,
    onSuccess?: (config: BundleDataHubConfiguration) => void
  ): void => {
    if (isNil(config)) return

    modal.input({
      label: t('data-hub.clone.name'),
      onOk: async (value: string) => {
        const configId = !isUndefined(config.id) ? String(config.id) : ''
        await cloneConfig({ name: value, originalName: configId })
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
  }, [cloneConfig, refetch, modal, t])

  const handleDelete = useCallback((
    config: BundleDataHubConfiguration,
    onSuccess?: () => void
  ): void => {
    if (isNil(config)) return

    modal.confirm({
      title: t('delete'),
      content: t('data-hub.delete.confirm', { name: config.text }),
      onOk: async () => {
        const configId = !isUndefined(config.id) ? String(config.id) : ''
        await deleteConfig({ name: configId })
        await refetch()

        if (!isNil(onSuccess)) {
          onSuccess()
        }
      }
    })
  }, [deleteConfig, refetch, modal, t])

  return {
    handleAdd,
    handleClone,
    handleDelete
  }
}
