/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useMemo } from 'react'
import { Tabs, Content } from '@pimcore/studio-ui-bundle/components'
import { type BundleDataHubConfiguration } from '../../config-api-slice-enhanced'
import { useAdapterIcon } from '../../hooks/use-adapter-icon'
import { isUndefined } from 'lodash'
import { getAdapterTypeString } from '../../utils/adapter-helpers'

interface ConfigTabsProps {
  openedConfigs: BundleDataHubConfiguration[]
  activeTabKey: string | undefined
  configurationsData?: { items: BundleDataHubConfiguration[] }
  onChangeTab: (key: string) => void
  onCloseTab: (key: string) => void
}

const TabContent = ({ config }: { config: BundleDataHubConfiguration }): React.JSX.Element => {
  return (
    <Content>
      <div>Configuration: {config.text}</div>
    </Content>
  )
}

const TabItem = ({ config }: { config: BundleDataHubConfiguration }): React.JSX.Element => {
  const icon = useAdapterIcon(getAdapterTypeString(config.adapter as string | undefined))

  return <>{icon}</>
}

export const ConfigTabs = ({
  openedConfigs,
  activeTabKey,
  configurationsData,
  onChangeTab,
  onCloseTab
}: ConfigTabsProps): React.JSX.Element => {
  const tabItems = useMemo(() => {
    // Recursively collect all config IDs from the tree
    const collectConfigIds = (items: BundleDataHubConfiguration[]): Set<string> => {
      const ids = new Set<string>()
      items.forEach(item => {
        ids.add(item.id)
        if (item.children) {
          collectConfigIds(item.children).forEach(id => ids.add(id))
        }
      })
      return ids
    }

    const existingConfigIds = configurationsData?.items 
      ? collectConfigIds(configurationsData.items)
      : new Set<string>()

    return openedConfigs
      .filter(config => existingConfigIds.has(config.id))
      .map((config) => ({
        key: config.id,
        label: config.text,
        icon: <TabItem config={ config } />,
        children: <TabContent config={ config } />
      }))
  }, [configurationsData, openedConfigs])

  if (isUndefined(activeTabKey)) {
    return <Content none />
  }

  return (
    <Tabs
      activeKey={ activeTabKey }
      items={ tabItems }
      onChange={ onChangeTab }
      onEdit={ onCloseTab }
      type="editable-card"
    />
  )
}
