/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { createContext, useContext } from 'react'
import { Tag } from 'antd'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type FormItemAnnotationStatus } from '../../../change-control/studio-form-annotations'

/** a review's marks for one grid's rows, keyed by entity id (a generic type by its name); empty outside a review */
export type RowMarks = Record<string, FormItemAnnotationStatus>

export type SchemaGrid = 'query' | 'mutation' | 'genericTypes'

export const SchemaRowMarksContext = createContext<Partial<Record<SchemaGrid, RowMarks>>>({})

export const useRowMarks = (grid: SchemaGrid): RowMarks => useContext(SchemaRowMarksContext)[grid] ?? {}

/** a review's fields before the change, by grid and entity id, for the entities whose fields it changes */
export const SchemaPreviousColumnsContext = createContext<Partial<Record<'query' | 'mutation', Record<string, unknown[]>>>>({})

/** the entity's fields before the change; undefined outside a review or when the review leaves them as they are */
export const usePreviousColumns = (grid: 'query' | 'mutation', entity: string): unknown[] | undefined =>
  useContext(SchemaPreviousColumnsContext)[grid]?.[entity]

// the colours of Studio's form annotation tag, so a row reads as a marked field does
const COLOR: Record<FormItemAnnotationStatus, string> = { added: 'green', changed: 'gold', removed: 'red', moved: 'geekblue' }

export const RowMark = ({ status }: { status?: FormItemAnnotationStatus }): React.JSX.Element | null => {
  const { t } = useTranslation()
  if (status === undefined) return null
  return (
    <Tag
      color={ COLOR[status] }
      data-testid={ `row-mark-${status}` }
    >{ t(`form.annotation.${status}`) }</Tag>
  )
}

/** the rows to show: the grid's own, then the entities the review removes, which the form no longer holds */
export const withRemovedRows = <T extends { id: string, entity: string }>(value: T[], marks: RowMarks): T[] => [
  ...value,
  ...Object.keys(marks)
    .filter((id) => marks[id] === 'removed' && !value.some((row) => row.id === id))
    .map((id) => ({ id, entity: id }) as unknown as T)
]
