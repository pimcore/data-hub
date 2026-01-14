/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { createContext, useContext, type ReactNode } from 'react'
import { type BundleDataHubConfigCollectionApiResponse } from '../config-api-slice-enhanced'
import { isUndefined } from 'lodash'

interface ConfigContextValue {
  configurationsData?: BundleDataHubConfigCollectionApiResponse
  isLoading: boolean
  isFetching: boolean
  refetch: () => Promise<{ data?: BundleDataHubConfigCollectionApiResponse }>
  expandedKeys: string[]
  setExpandedKeys: (keys: string[]) => void
}

const ConfigContext = createContext<ConfigContextValue | undefined>(undefined)

interface ConfigProviderProps {
  children: ReactNode
  value: ConfigContextValue
}

export const ConfigProvider = ({ children, value }: ConfigProviderProps): React.JSX.Element => {
  return (
    <ConfigContext.Provider value={ value }>
      {children}
    </ConfigContext.Provider>
  )
}

export const useConfigContext = (): ConfigContextValue => {
  const context = useContext(ConfigContext)

  if (isUndefined(context)) {
    throw new Error('useConfigContext must be used within ConfigProvider')
  }

  return context
}
