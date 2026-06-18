/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React from 'react'
import { ImportModal, IconButton, Tooltip } from '@pimcore/studio-ui-bundle/components'
import { getPrefix } from '@pimcore/studio-ui-bundle/api'
import { container, useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type BundleDataHubConfiguration } from '../../config-api-slice.gen'
import { findConfigInTree } from '../../utils/tree-helpers'
import { isNil, isUndefined } from 'lodash'
import { bundleServiceIds } from '../../../../config/service-ids'
import { type DynamicTypeDataHubAdapterRegistry } from '../../dynamic-types/dynamic-type-data-hub-adapter-registry'

interface ImportResponse {
  success: boolean
  type: string
  name: string
}

interface ImportButtonProps {
  onRefresh: () => Promise<{ data?: { items?: BundleDataHubConfiguration[] } }>
  handleOpenConfig: (config: BundleDataHubConfiguration) => void
  disabled?: boolean
}

export const ImportButton = ({
  onRefresh,
  handleOpenConfig,
  disabled = false
}: ImportButtonProps): React.JSX.Element => {
  const { t } = useTranslation()

  const adapterRegistry = container.get<DynamicTypeDataHubAdapterRegistry>(bundleServiceIds['DataHub/DynamicTypes/Adapter/Registry'])

  const handleImportSuccess = async (data: ImportResponse): Promise<void> => {
    const { data: updatedData } = await onRefresh()

    if (!isUndefined(updatedData?.items)) {
      const importedConfig = findConfigInTree(updatedData.items, (item) =>
        !isUndefined(item.id) && item.id === data.name
      )

      if (!isUndefined(importedConfig)) {
        handleOpenConfig(importedConfig)
      }
    }

    // Let the adapter run follow-up work (e.g. index generation) in a separate request,
    // now that the imported configuration is resolvable. No-op for adapters that don't override it.
    const adapter = adapterRegistry.getDynamicType(data.type, false)
    if (!isNil(adapter)) {
      await adapter.afterImport(data.name)
    }
  }

  return (
    <ImportModal
      accept=".json,application/json"
      action={ `${getPrefix()}/bundle/data-hub/config/import` }
      onUploadSuccess={ handleImportSuccess }
    >
      <Tooltip title={ t('tree.actions.import') }>
        <IconButton
          disabled={ disabled }
          icon={ { value: 'import' } }
          type="link"
        />
      </Tooltip>
    </ImportModal>
  )
}
