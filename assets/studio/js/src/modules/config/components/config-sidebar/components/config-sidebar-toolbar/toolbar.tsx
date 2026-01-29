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
import { bundleServiceIds } from '../../../../../../config/service-ids'
import { type DynamicTypeDataHubAdapterRegistry } from '../../../../dynamic-types/dynamic-type-data-hub-adapter-registry'
import { ImportButton } from '../../../import-button/import-button'
import { type BundleDataHubConfiguration } from '../../../../config-api-slice.gen'

interface ConfigSidebarToolbarProps {
  onAdd: (adapterType: string) => void
  onRefresh: () => Promise<{ data?: { items?: BundleDataHubConfiguration[] } }>
  handleOpenConfig: (config: BundleDataHubConfiguration) => void
  isFetching: boolean
}

export const ConfigSidebarToolbar = ({ onAdd, onRefresh, handleOpenConfig, isFetching }: ConfigSidebarToolbarProps): React.JSX.Element => {
  const { t } = useTranslation()
  const adapterRegistry = container.get<DynamicTypeDataHubAdapterRegistry>(bundleServiceIds['DataHub/DynamicTypes/Adapter/Registry'])

  const adapters = adapterRegistry.getDynamicTypes()

  const dropdownItems: DropdownProps['menu']['items'] = adapters.map((adapter) => ({
    key: adapter.id,
    label: t(adapter.getNameTranslationKey()),
    icon: <Icon { ...adapter.getIcon() } />,
    onClick: () => { onAdd(adapter.id) }
  }))

  return (
    <>
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
            disabled={ isFetching }
            handleOpenConfig={ handleOpenConfig }
            onRefresh={ onRefresh }
          />
        </Flex>

        <Dropdown
          menu={ { items: dropdownItems } }
          trigger={ ['click'] }
        >
          <DropdownButton>
            <Flex
              align='center'
              gap='extra-small'
            >
              <Icon value="new" />
              {t('new')}
            </Flex>
          </DropdownButton>
        </Dropdown>
      </Toolbar>
    </>
  )
}
