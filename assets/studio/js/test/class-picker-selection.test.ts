/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import {
  filterClassNames,
  selectClassNames,
  deselectClassNames,
  orderLike
} from '../src/modules/graphql/components/tabs/schema-definition-tab/class-picker-selection'

const classes = ['Car', 'Category', 'Customer', 'News']

describe('class picker selection', () => {
  it('filters class names case-insensitively and ignores surrounding blanks', () => {
    expect(filterClassNames(classes, ' cat ')).toEqual(['Category'])
    expect(filterClassNames(classes, 'C')).toEqual(['Car', 'Category', 'Customer'])
    expect(filterClassNames(classes, '')).toEqual(classes)
  })

  it('selects the given classes once, without classes already in the schema', () => {
    expect(selectClassNames(['Car'], ['Car', 'Category', 'News'], new Set(['News']))).toEqual(['Car', 'Category'])
  })

  it('deselects only the given classes, so selections hidden by the search are kept', () => {
    expect(deselectClassNames(['Car', 'News', 'Customer'], ['Car', 'Customer'])).toEqual(['News'])
  })

  it('orders the selection like the class list', () => {
    expect(orderLike(['News', 'Car'], classes)).toEqual(['Car', 'News'])
  })
})
