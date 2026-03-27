/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useMemo } from 'react'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type ItemType } from '@pimcore/studio-ui-bundle/components'
import { type GridColumnConfiguration } from '@pimcore/studio-ui-bundle/api/data-object'
import { isNil, isEmpty } from 'lodash'

export interface AddColumnDropdownMenu {
  items: ItemType[]
}

/**
 * Builds a nested Ant Design dropdown menu from a flat list of GridColumnConfiguration entries.
 */
export const useAddColumnDropdown = (
  availableColumns: GridColumnConfiguration[],
  onMenuItemClick: (column: GridColumnConfiguration) => void
): AddColumnDropdownMenu => {
  const { t } = useTranslation()

  return useMemo((): AddColumnDropdownMenu => {
    const groupTree: Record<string, any> = {}
    let menuIndex = 0

    availableColumns.forEach((column) => {
      let normalizedGroups: Array<string | string[]> = []
      if (Array.isArray(column.group)) {
        const hasNestedArrays = column.group.some((item: any) => Array.isArray(item))
        if (hasNestedArrays) {
          normalizedGroups = column.group
        } else {
          normalizedGroups = [column.group as unknown as string[]]
        }
      } else if (typeof column.group === 'string') {
        normalizedGroups = [column.group]
      } else {
        normalizedGroups = [String(column.group)]
      }

      normalizedGroups.forEach((groupPath) => {
        let groupParts: string[]
        if (typeof groupPath === 'string') {
          groupParts = groupPath.split('.')
        } else if (Array.isArray(groupPath)) {
          groupParts = groupPath.map((part: any) => String(part))
        } else {
          groupParts = [String(groupPath)]
        }

        let currentLevel = groupTree
        groupParts.forEach((part, index) => {
          if (isNil(currentLevel[part])) {
            currentLevel[part] = { items: [], subGroups: {} }
          }
          if (index === groupParts.length - 1) {
            currentLevel[part].items.push(column)
          } else {
            currentLevel = currentLevel[part].subGroups
          }
        })
      })
    })

    const convertTreeToMenuItems = (tree: Record<string, any>): ItemType[] => {
      return Object.entries(tree).map(([groupName, groupData]) => {
        const menuItem: any = { key: `group-${menuIndex++}`, label: t(groupName) }
        const subGroupItems = !isEmpty(Object.keys(groupData.subGroups as Record<string, any>))
          ? convertTreeToMenuItems(groupData.subGroups as Record<string, any>)
          : []
        const columnItems: ItemType[] = groupData.items.map((column: GridColumnConfiguration) => {
          let translationKey = column.key
          if (!isNil(column.config) && 'fieldDefinition' in column.config) {
            const fieldDefinition = column.config.fieldDefinition as Record<string, any>
            translationKey = fieldDefinition?.title ?? column.key
          }
          return { key: column.key, label: t(translationKey), onClick: () => { onMenuItemClick(column) } }
        })
        const allChildren = [...subGroupItems, ...columnItems]
        if (allChildren.length > 0) menuItem.children = allChildren
        return menuItem as ItemType
      })
    }

    return { items: convertTreeToMenuItems(groupTree) }
  }, [availableColumns, t, onMenuItemClick])
}
