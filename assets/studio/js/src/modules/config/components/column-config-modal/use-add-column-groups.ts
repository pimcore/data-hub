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
import { type ColumnPickerGroup } from '@pimcore/studio-ui-bundle/components'
import { type GridColumnConfiguration } from '@pimcore/studio-ui-bundle/api/data-object'
import { isNil } from 'lodash'
import { ADVANCED_COLUMN_KEY, ADVANCED_COLUMN_TYPE } from './types'

/**
 * Builds grouped, selectable column groups for the studio ColumnPicker from a
 * flat list of GridColumnConfiguration entries. Each leaf carries its column in
 * `meta`, so the picker's `onSelect` can hand it back to the add-column handler.
 */
export const useAddColumnGroups = (
  availableColumns: GridColumnConfiguration[]
): Array<ColumnPickerGroup<GridColumnConfiguration>> => {
  const { t } = useTranslation()

  return useMemo((): Array<ColumnPickerGroup<GridColumnConfiguration>> => {
    const groupTree: Record<string, any> = {}

    // The advanced column is offered through its own dedicated button, not the tree.
    const treeColumns = availableColumns.filter(
      (column) => column.key !== ADVANCED_COLUMN_KEY && column.type !== ADVANCED_COLUMN_TYPE
    )

    treeColumns.forEach((column) => {
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

    let groupIndex = 0

    const convertTreeToGroups = (
      tree: Record<string, any>
    ): Array<ColumnPickerGroup<GridColumnConfiguration>> => {
      return Object.entries(tree).reduce<Array<ColumnPickerGroup<GridColumnConfiguration>>>(
        (acc, [groupName, groupData]) => {
          const children = convertTreeToGroups(groupData.subGroups as Record<string, any>)

          const items = (groupData.items as GridColumnConfiguration[]).map((column) => {
            let translationKey = column.key
            if (!isNil(column.config) && 'fieldDefinition' in column.config) {
              const fieldDefinition = column.config.fieldDefinition as Record<string, any>
              translationKey = fieldDefinition?.title ?? column.key
            }
            return { key: column.key, label: t(translationKey), meta: column }
          })

          if (items.length > 0 || children.length > 0) {
            acc.push({ key: `group-${groupIndex++}`, label: t(groupName), items, children })
          }

          return acc
        },
        []
      )
    }

    return convertTreeToGroups(groupTree)
  }, [availableColumns, t])
}
