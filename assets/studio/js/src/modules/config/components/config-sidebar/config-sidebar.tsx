/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useEffect, useState, useMemo } from 'react'
import { isNil, isUndefined } from 'lodash'
import {
  Content,
  ContentLayout, Icon,
  SearchInput, TreeElement,
  type TreeDataItem
} from '@pimcore/studio-ui-bundle/components'
import { container, useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type BundleDataHubConfiguration } from '../../config-api-slice-enhanced'
import { ConfigSidebarToolbar } from './components/config-sidebar-toolbar/toolbar'
import { type DynamicTypeDataHubAdapterRegistry } from '../../dynamic-types/dynamic-type-data-hub-adapter-registry'
import { bundleServiceIds } from '../../../../config/service-ids'
import { useConfigContext } from '../../providers/config-provider'
import { useDataHubConfig } from '../../hooks/use-data-hub-config'
import { findConfigById, filterConfigsRecursive } from '../../utils/tree-helpers'
import { hasValidAdapter } from '../../utils/adapter-helpers'
import { canCreateAdapter } from '../../utils/permission-helpers'
import { getExportUrl } from '../../utils/get-export-url'
import { useStyles } from './config-sidebar.styles'

interface ConfigSidebarProps {
  handleOpenConfig: (config: BundleDataHubConfiguration) => void
}

export const ConfigSidebar = ({
  handleOpenConfig
}: ConfigSidebarProps): React.JSX.Element => {
  const { configurationsData, isLoading, isFetching, refetch, expandedKeys, setExpandedKeys } = useConfigContext()
  const [configListData, setConfigListData] = useState<BundleDataHubConfiguration[]>([])
  const [filteredData, setFilteredData] = useState<BundleDataHubConfiguration[]>([])
  const [searchValue, setSearchValue] = useState('')
  const [treeKey, setTreeKey] = useState(0)

  const { handleAdd, handleClone, handleDelete } = useDataHubConfig({ refetch })

  useEffect(() => {
    if (!isNil(configurationsData?.items)) {
      setConfigListData(configurationsData.items)
      setFilteredData(configurationsData.items)
      setTreeKey(prev => prev + 1)
    }
  }, [configurationsData])

  useEffect(() => {
    if (searchValue === '') {
      setFilteredData(configListData)
    } else {
      setFilteredData(filterConfigsRecursive(configListData, searchValue))
    }
  }, [searchValue, configListData])

  const { t } = useTranslation()
  const { styles } = useStyles()

  const adapterRegistry = container.get<DynamicTypeDataHubAdapterRegistry>(bundleServiceIds['DataHub/DynamicTypes/Adapter/Registry'])

  const getTreeItemIcon = (item: BundleDataHubConfiguration): React.JSX.Element | undefined => {
    if (item.allowChildren === true) {
      return <Icon value="folder" />
    }

    const adapterType = item.adapter as string | undefined
    if (isUndefined(adapterType)) {
      return undefined
    }

    const adapter = adapterRegistry.getDynamicType(adapterType, false)
    return isUndefined(adapter) ? undefined : <Icon { ...adapter.getIcon() } />
  }

  const getTreeItemActions = (item: BundleDataHubConfiguration): Array<{ key: string, icon: string }> => {
    if (item.allowChildren === true) {
      return []
    }

    const permissions = (item.permissions ?? {}) as { delete?: boolean }
    const actions: Array<{ key: string, icon: string }> = []

    // Clone needs create permission for the adapter type and a writeable config; the backend also
    // enforces that the store can accept a new configuration.
    if (item.writable && canCreateAdapter(item.adapter as string)) {
      actions.push({ key: 'clone', icon: 'copy-03' })
    }

    actions.push({ key: 'export', icon: 'export' })

    // Delete requires the per-config delete permission and a writeable config
    // (mirrors ConfigurationService::deleteConfiguration).
    if (item.writable && permissions.delete === true) {
      actions.push({ key: 'delete', icon: 'trash' })
    }

    return actions
  }

  const transformToTreeData = (items: BundleDataHubConfiguration[] | null): TreeDataItem[] => {
    if (isNil(items)) {
      return []
    }

    return items
      .filter((item) => {
        if (item.allowChildren === true) return true

        return hasValidAdapter(item.adapter as string | undefined, adapterRegistry)
      })
      .sort((a, b) => {
        return a.text.localeCompare(b.text, undefined, { sensitivity: 'base' })
      })
      .map((item) => {
        return {
          key: isUndefined(item.id) ? '' : String(item.id),
          title: item.text,
          icon: getTreeItemIcon(item),
          children: isUndefined(item.children) ? undefined : transformToTreeData(item.children),
          isLeaf: item.leaf,
          actions: getTreeItemActions(item),
          allowDrag: false,
          allowDrop: false
        }
      })
  }

  const treeData = useMemo(() => transformToTreeData(filteredData), [filteredData])

  const handleAddWrapper = (adapterType: string): void => {
    handleAdd(adapterType, handleOpenConfig)
  }

  const handleCloneWrapper = (key: string): void => {
    const config = findConfigById(key, configListData)
    if (!isNil(config)) {
      handleClone(config, handleOpenConfig)
    }
  }

  const handleDeleteWrapper = (key: string): void => {
    const config = findConfigById(key, configListData)
    if (!isNil(config)) {
      handleDelete(config)
    }
  }
  const handleExportWrapper = (key: string): void => {
    const config = findConfigById(key, configListData)
    if (!isNil(config)) {
      window.location.href = getExportUrl(config.text)
    }
  }

  const handleActionsClick = (key: string, action: string): void => {
    switch (action) {
      case 'clone':
        handleCloneWrapper(key)
        break
      case 'export':
        handleExportWrapper(key)
        break
      case 'delete':
        handleDeleteWrapper(key)
        break
    }
  }

  const handleTreeItemClick = (key: string): void => {
    const config = findConfigById(key, configListData)
    if (!isNil(config)) {
      if (config.allowChildren === true) {
        const currentKeys = expandedKeys
        if (!isNil(currentKeys) && currentKeys.includes(key)) {
          setExpandedKeys(currentKeys.filter(k => k !== key))
        } else {
          setExpandedKeys([...currentKeys, key])
        }
      } else {
        handleOpenConfig(config)
      }
    }
  }

  return (
    <ContentLayout
      renderToolbar={
        <ConfigSidebarToolbar
          handleOpenConfig={ handleOpenConfig }
          isFetching={ isFetching }
          onAdd={ handleAddWrapper }
          onRefresh={ refetch }
        />
      }
    >
      <Content
        loading={ isLoading }
        padded
      >
        <SearchInput
          onChange={ (e) => { setSearchValue(e.target.value) } }
          placeholder={ t('search') }
          withoutAddon
        />

        <Content
          loading={ isFetching }
          none={ filteredData.length === 0 }
        >

          <TreeElement
            className={ styles.treeContainer }
            defaultExpandedKeys={ expandedKeys }
            key={ `config-tree-${treeKey}` }
            onActionsClick={ handleActionsClick }
            onExpand={ (keys) => { setExpandedKeys(keys as string[]) } }
            onSelected={ (key) => { handleTreeItemClick(String(key)) } }
            treeData={ treeData }
          />

        </Content>
      </Content>
    </ContentLayout>
  )
}
