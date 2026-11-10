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
  collectAttributeOrder,
  sortColumnsByLayout
} from '../js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/sort-columns-by-layout.ts'

const tree = [
  {
    key: 'object-columns',
    children: [
      {
        key: 'panel',
        children: [
          { attribute: 'name', isFieldDefinition: true },
          { attribute: 'localizedfields', isFieldDefinition: false, children: [{ attribute: 'title', isFieldDefinition: true }] },
          { attribute: 'price', isFieldDefinition: true }
        ]
      }
    ]
  },
  { key: 'system-columns', children: [{ attribute: 'id', isFieldDefinition: true }] },
  { key: 'brick-container-bricks', children: [{ key: 'brick-group', children: [{ attribute: 'Engine~power', isFieldDefinition: true }] }] }
]

const column = attribute => ({ isOperator: false, attributes: { attribute } })
const operator = label => ({ isOperator: true, attributes: { label } })
const attributesOf = columns => columns.map(c => c.attributes.attribute ?? c.attributes.label)

describe('sort columns by layout', () => {
  it('lists the attributes in tree order, through panels and containers', () => {
    assert.deepEqual(collectAttributeOrder(tree), ['name', 'title', 'price', 'id', 'Engine~power'])
  })

  it('sorts attribute columns like the class layout', () => {
    const order = collectAttributeOrder(tree)
    const columns = [column('Engine~power'), column('id'), column('price'), column('name'), column('title')]
    assert.deepEqual(attributesOf(sortColumnsByLayout(columns, order)), ['name', 'title', 'price', 'id', 'Engine~power'])
  })

  it('keeps operators and unknown attributes at their index', () => {
    const order = collectAttributeOrder(tree)
    const columns = [column('price'), operator('Concat'), column('name'), column('removedField')]
    assert.deepEqual(attributesOf(sortColumnsByLayout(columns, order)), ['name', 'Concat', 'price', 'removedField'])
  })

  it('returns the columns unchanged when they are already sorted', () => {
    const order = collectAttributeOrder(tree)
    const columns = [column('name'), column('price')]
    assert.deepEqual(sortColumnsByLayout(columns, order), columns)
  })
})
