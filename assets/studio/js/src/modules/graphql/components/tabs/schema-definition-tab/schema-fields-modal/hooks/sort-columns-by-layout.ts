/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { flatMap, isNil } from 'lodash'
import { type TreeNode } from '../types'
import { collectFieldDefinitionNodes } from './class-attributes-tree-helpers'

interface SortableColumn {
  isOperator?: boolean
  attributes?: { attribute?: string }
}

export const collectAttributeOrder = (nodes: TreeNode[]): string[] =>
  flatMap(collectFieldDefinitionNodes(nodes), node => isNil(node.attribute) ? [] : [node.attribute])

// Columns of an attribute in the layout trade places among themselves; operators and unknown attributes keep their index.
export const sortColumnsByLayout = <T extends SortableColumn>(columns: T[], attributeOrder: string[]): T[] => {
  const rank = new Map<string, number>(attributeOrder.map((attribute, index): [string, number] => [attribute, index]))
  const rankOf = (column: T): number | undefined =>
    column.isOperator === true ? undefined : rank.get(column.attributes?.attribute ?? '')

  const slots = columns.reduce<number[]>((indexes, column, index) => rankOf(column) === undefined ? indexes : [...indexes, index], [])
  const sorted: T[] = slots
    .map((index): T => columns[index])
    .sort((a, b) => (rankOf(a) ?? 0) - (rankOf(b) ?? 0))

  const result = [...columns]
  slots.forEach((slot, i) => { result[slot] = sorted[i] })
  return result
}
