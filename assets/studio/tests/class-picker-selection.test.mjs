/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

// Run with: node --test "tests/*.test.mjs" (Node >= 22.18 or 24, which strips TypeScript types natively)
import assert from 'node:assert/strict'
import { describe, it } from 'node:test'
import {
  filterClassNames,
  selectClassNames,
  deselectClassNames,
  orderLike
} from '../js/src/modules/graphql/components/tabs/schema-definition-tab/class-picker-selection.ts'

const classes = ['Car', 'Category', 'Customer', 'News']

describe('class picker selection', () => {
  it('filters class names case-insensitively and ignores surrounding blanks', () => {
    assert.deepEqual(filterClassNames(classes, ' cat '), ['Category'])
    assert.deepEqual(filterClassNames(classes, 'C'), ['Car', 'Category', 'Customer'])
    assert.deepEqual(filterClassNames(classes, ''), classes)
  })

  it('selects the given classes once, without classes already in the schema', () => {
    assert.deepEqual(selectClassNames(['Car'], ['Car', 'Category', 'News'], new Set(['News'])), ['Car', 'Category'])
  })

  it('deselects only the given classes, so selections hidden by the search are kept', () => {
    assert.deepEqual(deselectClassNames(['Car', 'News', 'Customer'], ['Car', 'Customer']), ['News'])
  })

  it('orders the selection like the class list', () => {
    assert.deepEqual(orderLike(['News', 'Car'], classes), ['Car', 'News'])
  })
})
