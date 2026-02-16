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
import { Tabs, Content, ContentLayout, Toolbar, IconButton, PortalSlot, Icon, Space, Tooltip } from '@pimcore/studio-ui-bundle/components'
import { type BundleDataHubConfiguration } from '../../config-api-slice-enhanced'
import { isUndefined, isNil } from 'lodash'
import { ConfigTabContent } from './config-tab-content'
import { useStyles } from './config-tabs.styles'
import { container, useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type DynamicTypeDataHubAdapterRegistry } from '../../dynamic-types/dynamic-type-data-hub-adapter-registry'
import { bundleServiceIds } from '../../../../config/service-ids'
import { ExportButton } from '../export-button'
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
  const { t } = useTranslation()
  const refetchFunctionsRef = useRef<Map<string, () => Promise<any>>>(new Map())
  const [isFetchingTab, setIsFetchingTab] = useState(false)
  const { refetch } = useConfigContext()
  const { handleDelete } = useDataHubConfig({ refetch })

  const handleRefetchReady = useCallback((configId: string, refetchFn: () => Promise<any>): void => {
    refetchFunctionsRef.current.set(configId, refetchFn)
  }, [])

  const handleRefresh = (): void => {
    if (!isNil(activeTabKey)) {
      const refetchFn = refetchFunctionsRef.current.get(activeTabKey)
      if (!isNil(refetchFn)) {
        setIsFetchingTab(true)
        refetchFn().then(() => { setIsFetchingTab(false) }).catch(() => { setIsFetchingTab(false) })
      }
    }
  }

  const handleDeleteWrapper = useCallback((): void => {
    if (!isNil(activeTabKey)) {
      const config = openedConfigs.find(c => c.id === activeTabKey)
      if (!isNil(config)) {
        handleDelete(config, () => {
          onCloseTab(activeTabKey)
        })
      }
    }
  }, [activeTabKey, openedConfigs, handleDelete, onCloseTab])

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
          onRefetchReady={ handleRefetchReady }
          setModifiedConfigs={ setModifiedConfigs }
                  />
      }))
  }, [configurationsData, openedConfigs, modifiedConfigs, handleRefetchReady, setModifiedConfigs])

  if (isUndefined(activeTabKey)) {
    return <Content none />
  }

  const portalId = 'data-hub-save-button'

  const activeConfig = openedConfigs.find(config => config.id === activeTabKey)
  const isActiveConfigWriteable = activeConfig?.writable !== false

  return (
    <ContentLayout
      renderToolbar={
        <Toolbar>
          <Space size="extra-small">
            <Tooltip title={ t('refresh') }>
              <IconButton
                disabled={ isFetchingTab }
                icon={ { value: 'refresh' } }
                onClick={ handleRefresh }
              />
            </Tooltip>
            <Tooltip title={ isActiveConfigWriteable ? t('delete') : t('config_not_writeable') }>
              <IconButton
                disabled={ !isActiveConfigWriteable }
                icon={ { value: 'trash' } }
                onClick={ handleDeleteWrapper }
              />
            </Tooltip>
            {!isNil(activeConfig) && (
              <ExportButton configName={ activeConfig.text } />
            )}
          </Space>
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
