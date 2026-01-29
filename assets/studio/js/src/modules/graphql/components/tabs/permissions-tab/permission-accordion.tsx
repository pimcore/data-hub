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
import { type Permission, type RoleItem, type UserItem } from './types'
import { useUserGetCollectionQuery } from '@pimcore/studio-ui-bundle/api/user'
import { useRoleGetCollectionQuery } from '@pimcore/studio-ui-bundle/api/role'
import { type AccordionItemType } from '@pimcore/studio-ui-bundle/components'
import { InlineDropdownPanel } from '../../inline-dropdown-panel/inline-dropdown-panel'

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

  const { data: roleList } = useRoleGetCollectionQuery()
  const { data: userList } = useUserGetCollectionQuery()

  const isRoles = type === 'roles'
  const items = isRoles ? roleList?.items : userList?.items

  const currentNames = useMemo(() => value.map((p: Permission) => p.name), [value])

  const options = useMemo(() => {
    return items
      ?.filter((item: RoleItem | UserItem) => {
        const name: string = isRoles ? (item as RoleItem).name : (item as UserItem).username
        return !currentNames.includes(name)
      })
      .map((item: RoleItem | UserItem) => ({
        value: item.id,
        label: (
          <Flex
            align="center"
            gap="mini"
          >
            <Icon value={ isRoles ? 'shield' : 'user' } />
            {isRoles ? (item as RoleItem).name : (item as UserItem).username}
          </Flex>
        ),
        searchValue: isRoles ? (item as RoleItem).name : (item as UserItem).username
      })) ?? []
  }, [items, currentNames, isRoles])

  const createPermission = (id: number): Permission | undefined => {
    if (isRoles) {
      const role = roleList?.items.find(r => r.id === id)
      if (isNil(role)) return undefined

      const existingRole = value.find((r: Permission) => r.name === role.name)
      if (!isNil(existingRole)) return undefined

      return {
        id: role.id,
        name: role.name,
        read: true,
        update: false,
        delete: false
      }
    } else {
      const user = userList?.items.find(u => u.id === id)
      if (isNil(user)) return undefined

      const existingUser = value.find((u: Permission) => u.name === user.username)
      if (!isNil(existingUser)) return undefined

      return {
        id: user.id,
        name: user.username,
        read: true,
        update: false,
        delete: false
      }
    }
  }

  const handleOpen = (): void => {
    setSelectedItems([])
    setOpenDropdown(true)
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
      <>
        <IconTextButton
          icon={ { value: 'plus-circle' } }
          onClick={ (e) => {
            e.stopPropagation()
            handleOpen()
          } }
        >
          {t('add')}
        </IconTextButton>
        {openDropdown && (
          <InlineDropdownPanel>
            <Select
              listHeight={ 150 }
              mode="multiple"
              onChange={ (values) => { setSelectedItems(values as number[]) } }
              optionFilterProp="searchValue"
              options={ options }
              placeholder={ t(isRoles ? 'data-hub.permissions.role' : 'data-hub.permissions.user') }
              placement="topLeft"
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
          </InlineDropdownPanel>
        )}
      </>
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
