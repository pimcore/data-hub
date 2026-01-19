/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useMemo, useRef, useState, useCallback } from 'react'
import { Tabs, Content, ContentLayout, Toolbar, IconButton, PortalSlot } from '@pimcore/studio-ui-bundle/components'
import { type BundleDataHubConfiguration } from '../../config-api-slice-enhanced'
import { useAdapterIcon } from '../../hooks/use-adapter-icon'
import { isUndefined, isNil } from 'lodash'
import { getAdapterTypeString } from '../../utils/adapter-helpers'
import { ConfigTabContent } from './config-tab-content'
import { useStyles } from './config-tabs.styles'

interface ConfigTabsProps {
  openedConfigs: BundleDataHubConfiguration[]
  activeTabKey: string | undefined
  configurationsData?: { items: BundleDataHubConfiguration[] }
  onChangeTab: (key: string) => void
  onCloseTab: (key: string) => void
  modifiedConfigs: string[]
  setModifiedConfigs: React.Dispatch<React.SetStateAction<string[]>>
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
  onCloseTab,
  modifiedConfigs,
  setModifiedConfigs
}: ConfigTabsProps): React.JSX.Element => {
  const { styles } = useStyles()
  const refetchFunctionsRef = useRef<Map<string, () => Promise<any>>>(new Map())
  const [isFetchingTab, setIsFetchingTab] = useState(false)

  const handleRefetchReady = useCallback((configId: string, refetchFn: () => Promise<any>): void => {
    refetchFunctionsRef.current.set(configId, refetchFn)
  }, [])

  const handleRefresh = (): void => {
    if (activeTabKey !== undefined) {
      const refetchFn = refetchFunctionsRef.current.get(activeTabKey)
      if (refetchFn !== undefined) {
        setIsFetchingTab(true)
        refetchFn().then(() => { setIsFetchingTab(false) }).catch(() => { setIsFetchingTab(false) })
      }
    }
  }

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

    const existingConfigIds = !isNil(configurationsData?.items)
      ? collectConfigIds(configurationsData.items)
      : new Set<string>()

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
          onRefetchReady={ handleRefetchReady }
          setModifiedConfigs={ setModifiedConfigs }
                  />
      }))
  }, [configurationsData, openedConfigs, modifiedConfigs, handleRefetchReady, setModifiedConfigs])

  if (isUndefined(activeTabKey)) {
    return <Content none />
  }

  const portalId = 'data-hub-save-button'

  return (
    <ContentLayout
      renderToolbar={
        <Toolbar>
          <IconButton
            disabled={ isFetchingTab }
            icon={ { value: 'refresh' } }
            onClick={ handleRefresh }
          />
          <PortalSlot id={ portalId } />
        </Toolbar>
      }
    >
      <Tabs
        activeKey={ activeTabKey }
        className={ styles.tabs }
        hasStickyHeader
        items={ tabItems }
        onChange={ onChangeTab }
        onClose={ onCloseTab }
        rootClassName={ styles.tabsContainer }
      />
    </ContentLayout>
  )
}
