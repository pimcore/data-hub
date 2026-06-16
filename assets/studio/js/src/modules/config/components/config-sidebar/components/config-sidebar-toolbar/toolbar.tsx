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
import { Toolbar, DropdownButton, Dropdown, type DropdownProps, IconButton, Icon, Flex, Tooltip } from '@pimcore/studio-ui-bundle/components'
import { container, useTranslation } from '@pimcore/studio-ui-bundle/app'
import { isAllowed } from '@pimcore/studio-ui-bundle/modules/auth'
import { bundleServiceIds } from '../../../../../../config/service-ids'
import { type DynamicTypeDataHubAdapterRegistry } from '../../../../dynamic-types/dynamic-type-data-hub-adapter-registry'
import { ImportButton } from '../../../import-button/import-button'
import { type BundleDataHubConfiguration } from '../../../../config-api-slice.gen'

const DATA_HUB_ADMIN_PERMISSION = 'plugin_datahub_admin'
const DATA_HUB_ADAPTER_PERMISSION_PREFIX = 'plugin_datahub_adapter_'

interface ConfigSidebarToolbarProps {
  onAdd: (adapterType: string) => void
  onRefresh: () => Promise<{ data?: { items?: BundleDataHubConfiguration[] } }>
  handleOpenConfig: (config: BundleDataHubConfiguration) => void
  isFetching: boolean
  configurationsWriteable?: boolean
}

export const ConfigSidebarToolbar = ({ onAdd, onRefresh, handleOpenConfig, isFetching, configurationsWriteable = true }: ConfigSidebarToolbarProps): React.JSX.Element => {
  const { t } = useTranslation()
  const adapterRegistry = container.get<DynamicTypeDataHubAdapterRegistry>(bundleServiceIds['DataHub/DynamicTypes/Adapter/Registry'])

  // A user may create a configuration of an adapter type when they are a data hub admin or
  // hold the adapter-specific permission for that type. Mirrors the backend create check.
  const canCreate = (adapterId: string): boolean =>
    isAllowed(DATA_HUB_ADMIN_PERMISSION) || isAllowed(`${DATA_HUB_ADAPTER_PERMISSION_PREFIX}${adapterId}`)

  const creatableAdapters = adapterRegistry.getDynamicTypes().filter((adapter) => canCreate(adapter.id))

  const dropdownItems: DropdownProps['menu']['items'] = creatableAdapters.map((adapter) => ({
    key: adapter.id,
    label: t(adapter.getNameTranslationKey()),
    icon: <Icon { ...adapter.getIcon() } />,
    onClick: () => { onAdd(adapter.id) }
  }))

  const newButton = (
    <DropdownButton disabled={ !configurationsWriteable }>
      <Flex
        align='center'
        gap='extra-small'
      >
        <Icon value="new" />
        {t('new')}
      </Flex>
    </DropdownButton>
  )

  return (
    <Toolbar>
      <Flex gap="extra-small">
        <Tooltip title={ t('refresh') }>
          <IconButton
            disabled={ isFetching }
            icon={ { value: 'refresh' } }
            onClick={ onRefresh }
            type="link"
          />
        </Tooltip>

        <ImportButton
          disabled={ isFetching || !configurationsWriteable }
          handleOpenConfig={ handleOpenConfig }
          onRefresh={ onRefresh }
        />
      </Flex>

      { dropdownItems.length > 0 && (
        configurationsWriteable
          ? (
            <Dropdown
              menu={ { items: dropdownItems } }
              trigger={ ['click'] }
            >
              { newButton }
            </Dropdown>
            )
          : (
            <Tooltip title={ t('data-hub.config.not-writeable') }>
              { newButton }
            </Tooltip>
            )
      ) }
    </Toolbar>
  )
}
