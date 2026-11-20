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

/** what differs between the columns and the columns before the change; nothing without previous columns */
export const columnMarks = (columns: SchemaColumn[], previous?: SchemaColumn[]): ColumnMarks => {
  if (previous === undefined) return { marks: columns.map(() => undefined), removed: [] }

  const before = new Map<string, SchemaColumn>()
  for (const column of previous) before.set(identity(column), column)
  const now = new Set(columns.map(identity))

  return {
    marks: columns.map((column) => {
      const old = before.get(identity(column))
      if (old === undefined) return 'added'
      return isEqual(comparable(old), comparable(column)) ? undefined : 'changed'
    }),
    removed: previous.filter((column) => !now.has(identity(column)))
  }
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
