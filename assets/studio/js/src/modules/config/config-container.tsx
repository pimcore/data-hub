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
import { ConfigLayout } from '@pimcore/studio-ui-bundle/components'
import { ConfigSidebar } from './components/config-sidebar/config-sidebar'
import { ConfigTabs } from './components/tabs/config-tabs'
import { useBundleDataHubConfigCollectionQuery } from './config-api-slice-enhanced'
import { useTabManager } from './hooks/use-tab-manager'
import { ConfigProvider } from './providers/config-provider'

export const ConfigContainer = (): React.JSX.Element => {
  const { data: configurationsData, isLoading, isFetching, refetch } = useBundleDataHubConfigCollectionQuery()
  const { openedConfigs, activeTabKey, handleOpenConfig, handleCloseTab, handleChangeTab } = useTabManager()
  const [expandedKeys, setExpandedKeys] = useState<string[]>([])

  return (
    <ConfigProvider
      value={ {
        configurationsData,
        isLoading,
        isFetching,
        refetch,
        expandedKeys,
        setExpandedKeys
      } }
    >
      <ConfigLayout
        leftItem={ {
          children: (
            <ConfigSidebar handleOpenConfig={ handleOpenConfig } />
          )
        } }
        rightItem={ {
          children: (
            <ConfigTabs
              activeTabKey={ activeTabKey }
              configurationsData={ configurationsData }
              onChangeTab={ handleChangeTab }
              onCloseTab={ handleCloseTab }
              openedConfigs={ openedConfigs }
            />
          )
        } }
      />
    </ConfigProvider>
  )
}
