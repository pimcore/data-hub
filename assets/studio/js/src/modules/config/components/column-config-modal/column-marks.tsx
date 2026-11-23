/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useMemo } from 'react'
import { Tag } from 'antd'
import { isEqual } from 'lodash'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { columnIdentity } from './column-identity'
import { type StackListProps } from '@pimcore/studio-ui-bundle/components'
import { type SchemaColumn } from './types'

export type ColumnMarkStatus = 'added' | 'changed' | 'removed'

export interface ColumnMarks {
  /** one entry per column, in order: its mark, or undefined when unchanged */
  marks: Array<ColumnMarkStatus | undefined>
  /** the previous columns no longer present */
  removed: SchemaColumn[]
}

const identity = (column: SchemaColumn): string => columnIdentity(column, column.locale)

const comparable = (column: SchemaColumn): Record<string, unknown> => ({
  key: column.key,
  type: column.type,
  fieldtype: column.fieldtype,
  config: column.config ?? {},
  locale: column.locale ?? null
})

/**
 * what differs between the columns and the columns before the change; nothing without previous columns.
 * Columns can share an identity (legacy advanced columns, a field added twice): each previous column pairs with
 * at most one column, an unchanged one first, so neither a duplicate's change nor its removal is lost.
 */
export const columnMarks = (columns: SchemaColumn[], previous?: SchemaColumn[]): ColumnMarks => {
  if (previous === undefined) return { marks: columns.map(() => undefined), removed: [] }

  const unpaired = new Set(previous.map((_, index) => index))
  const pair = (column: SchemaColumn, accept: (old: SchemaColumn) => boolean): boolean => {
    const index = [...unpaired].find((candidate) => identity(previous[candidate]) === identity(column) && accept(previous[candidate]))
    if (index === undefined) return false
    unpaired.delete(index)
    return true
  }

  const unchanged = columns.map((column) => pair(column, (old) => isEqual(comparable(old), comparable(column))))
  const marks = columns.map((column, index): ColumnMarkStatus | undefined => {
    if (unchanged[index]) return undefined
    return pair(column, () => true) ? 'changed' : 'added'
  })

  return { marks, removed: previous.filter((_, index) => unpaired.has(index)) }
}

// the colours of Studio's form annotation tag, so a column reads as a marked field does
const COLOR: Record<ColumnMarkStatus, string> = { added: 'green', changed: 'gold', removed: 'red' }

export const ColumnMark = ({ status }: { status?: ColumnMarkStatus }): React.JSX.Element | null => {
  const { t } = useTranslation()
  if (status === undefined) return null
  return (
    <Tag
      color={ COLOR[status] }
      data-testid={ `column-mark-${status}` }
    >{ t(`form.annotation.${status}`) }</Tag>
  )
}

/** the marks of the editor's columns, and the removed columns as list rows; nothing unless read only, where the draft is the columns as given */
export const useColumnMarkItems = (
  columns: SchemaColumn[],
  previous: SchemaColumn[] | undefined,
  readOnly: boolean
): { marks: ColumnMarks['marks'], removedItems: StackListProps['items'] } => useMemo(() => {
  const { marks, removed } = columnMarks(columns, readOnly ? previous : undefined)
  return {
    marks,
    removedItems: removed.map((column, index) => ({
      id: `removed-${index}`,
      sortable: false,
      children: <Tag>{ column.key }</Tag>,
      renderRightToolbar: <ColumnMark status='removed' />
    }))
  }
}, [columns, previous, readOnly])
