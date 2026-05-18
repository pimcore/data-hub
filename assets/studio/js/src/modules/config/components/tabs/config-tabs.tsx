/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useMemo, useCallback } from 'react'
import { Tabs, Content, Icon } from '@pimcore/studio-ui-bundle/components'
import { type BundleDataHubConfiguration } from '../../config-api-slice-enhanced'
import { isUndefined, isNil } from 'lodash'
import { ConfigTabContent } from './config-tab-content'
import { useStyles } from './config-tabs.styles'
import { container } from '@pimcore/studio-ui-bundle/app'
import { type DynamicTypeDataHubAdapterRegistry } from '../../dynamic-types/dynamic-type-data-hub-adapter-registry'
import { bundleServiceIds } from '../../../../config/service-ids'
import { useDataHubConfig } from '../../hooks/use-data-hub-config'
import { useConfigContext } from '../../providers/config-provider'

interface ConfigTabsProps {
  openedConfigs: BundleDataHubConfiguration[]
  activeTabKey: string | undefined
  configurationsData?: { items: BundleDataHubConfiguration[] }
  onChangeTab: (key: string) => void
  onCloseTab: (key: string) => void
  modifiedConfigs: string[]
  setModifiedConfigs: React.Dispatch<React.SetStateAction<string[]>>
}

const TabItem = ({ config }: { config: BundleDataHubConfiguration }): React.JSX.Element | null => {
  const adapterRegistry = container.get<DynamicTypeDataHubAdapterRegistry>(bundleServiceIds['DataHub/DynamicTypes/Adapter/Registry'])
  const adapterType = config.adapter as string | undefined

  if (isUndefined(adapterType)) {
    return null
  }

  const adapter = adapterRegistry.getDynamicType(adapterType, false)
  return isUndefined(adapter) ? null : <Icon { ...adapter.getIcon() } />
}

export const ConfigTabs = ({
  openedConfigs,
  activeTabKey,
  configurationsData,
  onChangeTab,
  onCloseTab,
  modifiedConfigs,
  setModifiedConfigs
}: ConfigTabsProps): React.JSX.Element => {
  const { styles } = useStyles()
  const { refetch } = useConfigContext()
  const { handleDelete } = useDataHubConfig({ refetch })

  const handleDeleteConfig = useCallback((configId: string): void => {
    const config = openedConfigs.find(c => c.id === configId)
    if (!isNil(config)) {
      handleDelete(config, () => {
        onCloseTab(configId)
      })
    }
  }, [openedConfigs, handleDelete, onCloseTab])

  const tabItems = useMemo(() => {
    // Recursively collect all config IDs from the tree
    const collectConfigIds = (items: BundleDataHubConfiguration[]): Set<string> => {
      const ids = new Set<string>()
      items.forEach(item => {
        ids.add(item.id)
        if (!isNil(item.children)) {
          collectConfigIds(item.children).forEach(id => ids.add(id))
        }
      })
      return ids
    }

    const existingConfigIds = isNil(configurationsData?.items)
      ? new Set<string>()
      : collectConfigIds(configurationsData.items)

    return openedConfigs
      .filter(config => !isNil(existingConfigIds) && existingConfigIds.has(config.id))
      .map((config) => ({
        key: config.id,
        label: `${config.text} ${modifiedConfigs.includes(config.id) ? '*' : ''}`,
        icon: <TabItem config={ config } />,
        children: <ConfigTabContent
          config={ config }
          isActive={ activeTabKey === config.id }
          modifiedConfigs={ modifiedConfigs }
          onDelete={ () => { handleDeleteConfig(config.id) } }
          setModifiedConfigs={ setModifiedConfigs }
                  />
      }))
  }, [configurationsData, openedConfigs, modifiedConfigs, setModifiedConfigs, activeTabKey, handleDeleteConfig])

  if (isUndefined(activeTabKey)) {
    return <Content none />
  }

  return (
    <Tabs
      activeKey={ activeTabKey }
      className={ styles.tabs }
      hasStickyHeader
      items={ tabItems }
      onChange={ onChangeTab }
      onClose={ onCloseTab }
      rootClassName={ styles.tabsContainer }
    />
  )
}
