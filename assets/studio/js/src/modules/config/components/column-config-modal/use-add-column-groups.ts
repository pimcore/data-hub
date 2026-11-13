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
import { CLASSIFICATION_STORE_TYPE, PICK_KEY_SUFFIX } from './column-identity'
import { ADVANCED_COLUMN_KEY, ADVANCED_COLUMN_TYPE } from './types'

type PickerItem = ColumnPickerGroup<GridColumnConfiguration>['items'][number]

/**
 * The leaves one column is offered as. A store field is offered once per key, the ids keeping those leaves
 * apart; the whole-store entry carries a sibling leaf which opens the key picker, when the caller can host one.
 */
const idText = (value: unknown): string => typeof value === 'number' || typeof value === 'string' ? String(value) : ''

const leavesOf = (column: GridColumnConfiguration, t: (key: string) => string, withKeyPicker: boolean): PickerItem[] => {
  let translationKey: string = column.key
  if (!isNil(column.config) && 'fieldDefinition' in column.config) {
    const fieldDefinition = column.config.fieldDefinition as Record<string, any>
    translationKey = fieldDefinition?.title ?? column.key
  }
  const storeIds = column.type === CLASSIFICATION_STORE_TYPE ? column.config as Record<string, unknown> | undefined : undefined
  const keyId = storeIds?.keyId
  const ids = keyId === undefined ? '' : `-${idText(storeIds?.groupId)}-${idText(keyId)}`
  const item = { key: `${column.key}${ids}`, label: t(translationKey), meta: column }
  if (storeIds === undefined || keyId !== undefined || !withKeyPicker) return [item]
  const pickLabel = `${t('data-hub.column-config-modal.pick-classification-store-key')} (${t(translationKey)})`
  return [item, { key: `${column.key}${PICK_KEY_SUFFIX}`, label: pickLabel, meta: column }]
}

interface ConversionContext {
  t: (key: string) => string
  withKeyPicker: boolean
  nextIndex: number
}

interface GroupNode {
  items: GridColumnConfiguration[]
  subGroups: Record<string, GroupNode>
}

const convertTreeToGroups = (
  tree: Record<string, GroupNode>,
  context: ConversionContext
): Array<ColumnPickerGroup<GridColumnConfiguration>> => {
  const groups: Array<ColumnPickerGroup<GridColumnConfiguration>> = []
  for (const [groupName, groupData] of Object.entries(tree)) {
    const children = convertTreeToGroups(groupData.subGroups, context)
    const items = groupData.items.flatMap(column => leavesOf(column, context.t, context.withKeyPicker))
    if (items.length > 0 || children.length > 0) {
      groups.push({ key: `group-${context.nextIndex++}`, label: context.t(groupName), items, children })
    }
  }
  return groups
}

/**
 * Builds grouped, selectable column groups for the studio ColumnPicker from a
 * flat list of GridColumnConfiguration entries. Each leaf carries its column in
 * `meta`, so the picker's `onSelect` can hand it back to the add-column handler.
 */
export const useAddColumnGroups = (
  availableColumns: GridColumnConfiguration[],
  /** Offer the sibling leaf which opens the key picker; only a caller mounting the picker provider with a class may */
  withKeyPicker = false
): Array<ColumnPickerGroup<GridColumnConfiguration>> => {
  const { t } = useTranslation()

  return useMemo((): Array<ColumnPickerGroup<GridColumnConfiguration>> => {
    const groupTree: Record<string, GroupNode> = {}

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

    return convertTreeToGroups(groupTree, { t, withKeyPicker, nextIndex: 0 })
  }, [availableColumns, t, withKeyPicker])
}
