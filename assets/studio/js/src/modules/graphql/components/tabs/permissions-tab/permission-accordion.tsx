/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useState, useMemo } from 'react'
import { Flex, IconTextButton, Select, Button, Icon, OperationalGrid, Accordion } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { isNil } from 'lodash'
import { type Permission } from './types'
import { type AccordionItemType } from '@pimcore/studio-ui-bundle/components'
import { InlineDropdownPanel } from '../../inline-dropdown-panel/inline-dropdown-panel'
import { useBundleDataHubUsersCollectionQuery } from '../../../../config/users-api-slice.gen'

interface PermissionAccordionProps {
  type: 'roles' | 'users'
  value?: Permission[]
  onChange?: (value: Permission[]) => void
}

export const PermissionAccordion = ({
  type,
  value = [],
  onChange
}: PermissionAccordionProps): React.JSX.Element => {
  const { t } = useTranslation()
  const [openDropdown, setOpenDropdown] = useState(false)
  const [selectedItems, setSelectedItems] = useState<number[]>([])

  const isRoles = type === 'roles'
  const { data: roleList } = useBundleDataHubUsersCollectionQuery({ type: 'role' }, { skip: !isRoles, refetchOnMountOrArgChange: true })
  const { data: userList } = useBundleDataHubUsersCollectionQuery({ type: 'user' }, { skip: isRoles, refetchOnMountOrArgChange: true })

  const items = isRoles ? roleList?.items : userList?.items

  const currentNames = useMemo(() => value.map((p: Permission) => p.name), [value])

  const options = useMemo(() => {
    return items
      ?.filter((item) => {
        const name: string = item.text
        return !currentNames.includes(name)
      })
      .map((item) => ({
        value: item.id,
        label: (
          <Flex
            align="center"
            gap="mini"
          >
            <Icon value={ isRoles ? 'shield' : 'user' } />
            {item.text}
          </Flex>
        ),
        searchValue: item.text
      })) ?? []
  }, [items, currentNames, isRoles])

  const createPermission = (id: number): Permission | undefined => {
    const item = items?.find(i => i.id === id)
    if (isNil(item)) return undefined

    const existingPermission = value.find((p: Permission) => p.name === item.text)
    if (!isNil(existingPermission)) return undefined

    return {
      id: item.id,
      name: item.text,
      read: true,
      update: false,
      delete: false
    }
  }

  const handleOpenChange = (open: boolean): void => {
    setSelectedItems([])
    setOpenDropdown(open)
  }

  const handleCancel = (): void => {
    setSelectedItems([])
    setOpenDropdown(false)
  }

  const handleApply = (): void => {
    if (selectedItems.length > 0) {
      const newPermissions = selectedItems
        .map(id => createPermission(id))
        .filter((p): p is Permission => !isNil(p))

      if (!isNil(onChange) && newPermissions.length > 0) {
        onChange([...value, ...newPermissions])
      }
    }
    setSelectedItems([])
    setOpenDropdown(false)
  }

  const accordionItem: AccordionItemType = useMemo(() => ({
    key: type,
    id: type,
    title: <>{t(isRoles ? 'data-hub.permissions.role-permissions' : 'data-hub.permissions.user-permissions')}</>,
    info: (
      <InlineDropdownPanel
        content={
          <>
            <Select
              getPopupContainer={ (triggerNode: HTMLElement) => triggerNode.parentElement ?? document.body }
              listHeight={ 150 }
              mode="multiple"
              onChange={ (values) => { setSelectedItems(values as number[]) } }
              optionFilterProp="searchValue"
              options={ options }
              placeholder={ t(isRoles ? 'data-hub.permissions.role' : 'data-hub.permissions.user') }
              showSearch
              style={ { width: '400px' } }
              value={ selectedItems }
            />
            <Flex
              gap="small"
              justify="flex-end"
              style={ { marginTop: '12px' } }
            >
              <Button
                onClick={ handleCancel }
                type="default"
              >
                {t('button.cancel')}
              </Button>
              <Button
                onClick={ handleApply }
                type="primary"
              >
                {t('button.apply')}
              </Button>
            </Flex>
          </>
        }
        onOpenChange={ handleOpenChange }
        open={ openDropdown }
      >
        <IconTextButton
          icon={ { value: 'plus-circle' } }
          onClick={ (e) => { e.stopPropagation() } }
        >
          {t('add')}
        </IconTextButton>
      </InlineDropdownPanel>
    ),
    children: (
      <OperationalGrid.Grid />
    )
  }), [type, openDropdown, options, selectedItems])

  return (
    <Accordion
      activeKey={ type }
      bordered
      collapsible="icon"
      items={ [accordionItem] }
      size="small"
      table
    />
  )
}
