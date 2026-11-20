/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { isEqual } from 'lodash'
import { type PersistedColumnConfig } from './types'

export type FieldMarkStatus = 'added' | 'changed' | 'removed'

export interface FieldMarks {
  /** one entry per field, in order: its mark, or undefined when unchanged */
  marks: Array<FieldMarkStatus | undefined>
  /** the fields before the change that are no longer there */
  removed: PersistedColumnConfig[]
}

// a field is its class attribute, an operator its class: a changed label or child still reads as the same field
const identity = (column: PersistedColumnConfig): string =>
  column.isOperator ? `operator:${column.attributes?.class ?? ''}` : `field:${column.attributes?.attribute ?? ''}`

// keys, an operator's children's too, are regenerated whenever a client writes the fields, so they say nothing about a change
const comparable = (column: PersistedColumnConfig): unknown =>
  JSON.parse(JSON.stringify({ isOperator: column.isOperator, attributes: column.attributes }, (name, value) => name === 'key' ? undefined : value))

/**
 * what differs between an entity's fields and its fields before the change; nothing without previous fields.
 * Fields can share an identity (one attribute twice, two operators of a class): each previous field pairs with at
 * most one field, an unchanged one first, so neither a duplicate's change nor its removal is lost.
 */
export const fieldMarks = (columns: PersistedColumnConfig[], previous?: unknown[]): FieldMarks => {
  if (previous === undefined) return { marks: columns.map(() => undefined), removed: [] }

  const before = previous.filter((column): column is PersistedColumnConfig => typeof column === 'object' && column !== null)
  const unpaired = new Set(before.map((_, index) => index))
  const pair = (column: PersistedColumnConfig, accept: (old: PersistedColumnConfig) => boolean): boolean => {
    const index = [...unpaired].find((candidate) => identity(before[candidate]) === identity(column) && accept(before[candidate]))
    if (index === undefined) return false
    unpaired.delete(index)
    return true
  }

  const unchanged = columns.map((column) => pair(column, (old) => isEqual(comparable(old), comparable(column))))
  const marks = columns.map((column, index): FieldMarkStatus | undefined => {
    if (unchanged[index]) return undefined
    return pair(column, () => true) ? 'changed' : 'added'
  })

  return { marks, removed: before.filter((_, index) => unpaired.has(index)) }
}
