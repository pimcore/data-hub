/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useCallback } from 'react'
import { type BundleDataHubConfiguration } from '../../config-api-slice-enhanced'
import { isUndefined, isNil } from 'lodash'
import { container } from '@pimcore/studio-ui-bundle/app'
import { bundleServiceIds } from '../../../../config/service-ids'
import { type DynamicTypeDataHubAdapterRegistry } from '../../dynamic-types/dynamic-type-data-hub-adapter-registry'

interface ConfigTabContentProps {
  config: BundleDataHubConfiguration
  onDelete: () => void
  modifiedConfigs: string[]
  setModifiedConfigs: React.Dispatch<React.SetStateAction<string[]>>
  isActive: boolean
}

export const ConfigTabContent = ({ config, onDelete, modifiedConfigs, setModifiedConfigs, isActive }: ConfigTabContentProps): React.JSX.Element => {
  const adapterRegistry = container.get<DynamicTypeDataHubAdapterRegistry>(bundleServiceIds['DataHub/DynamicTypes/Adapter/Registry'])

  const handleChange = useCallback((isDirty: boolean) => {
    setModifiedConfigs((prev) => {
      const isCurrentlyModified = prev.includes(config.id)
      if (isDirty && !isCurrentlyModified) {
        return [...prev, config.id]
      } else if (!isDirty && isCurrentlyModified) {
        return prev.filter(id => id !== config.id)
      }
      return prev
    })
  }, [config.id, setModifiedConfigs])

  const adapterType = config.adapter as string | undefined

  if (isUndefined(adapterType)) {
    return <div>Unknown adapter type</div>
  }

  try {
    const adapter = adapterRegistry.getDynamicType(adapterType, false)
    if (isNil(adapter)) {
      return <div>Adapter not found: {adapterType}</div>
    }

    return adapter.renderDetailView({
      configName: config.text,
      configId: config.id,
      isActive,
      hasStudioColumnConfig: config.studioColumnConfig,
      onChange: handleChange,
      onDelete
    })
  } catch (err) {
    console.error('Error rendering form:', err)
    return <div>Error rendering adapter form</div>
  }
}
