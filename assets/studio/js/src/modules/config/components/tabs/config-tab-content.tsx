/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useEffect, useCallback } from 'react'
import { Content } from '@pimcore/studio-ui-bundle/components'
import { type BundleDataHubConfiguration, useBundleDataHubConfigGetQuery } from '../../config-api-slice-enhanced'
import { isUndefined, isNil } from 'lodash'
import { container } from '@pimcore/studio-ui-bundle/app'
import { bundleServiceIds } from '../../../../config/service-ids'
import { type DynamicTypeDataHubAdapterRegistry } from '../../dynamic-types/dynamic-type-data-hub-adapter-registry'

interface ConfigTabContentProps {
  config: BundleDataHubConfiguration
  onRefetchReady: (configId: string, refetchFn: () => Promise<any>) => void
  modifiedConfigs: string[]
  setModifiedConfigs: React.Dispatch<React.SetStateAction<string[]>>
  isActive: boolean
}

export const ConfigTabContent = ({ config, onRefetchReady, modifiedConfigs, setModifiedConfigs, isActive }: ConfigTabContentProps): React.JSX.Element => {
  const { data: configDetail, isLoading, isFetching, error, refetch } = useBundleDataHubConfigGetQuery(
    { name: config.text },
    { refetchOnMountOrArgChange: true }
  )
  const adapterRegistry = container.get<DynamicTypeDataHubAdapterRegistry>(bundleServiceIds['DataHub/DynamicTypes/Adapter/Registry'])

  // Register refetch function only once when component mounts
  useEffect(() => {
    onRefetchReady(config.id, refetch)
  }, [config.id, onRefetchReady])

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

  const renderContent = (): React.JSX.Element => {
    if (!isNil(error) || isNil(configDetail)) {
      return <div>Error loading configuration</div>
    }

    const adapterType = config.adapter as string | undefined

    if (isUndefined(adapterType)) {
      return <div>Unknown adapter type</div>
    }

    try {
      const adapter = adapterRegistry.getDynamicType(adapterType, false)
      if (isNil(adapter)) {
        return <div>Adapter not found: {adapterType}</div>
      }

      return adapter.getFormComponent({
        config: configDetail,
        configName: config.text,
        configId: config.id,
        onChange: handleChange,
        isActive
      })
    } catch (err) {
      console.error('Error rendering form:', err)
      return <div>Error rendering adapter form</div>
    }
  }

  return (
    <Content
      className="h-full"
      loading={ isLoading || isFetching }
      padded
    >
      {!isLoading && !isFetching && renderContent()}
    </Content>
  )
}
