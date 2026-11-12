/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type TreeNode } from '../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/types'
import {
  collectAttributeOrder,
  sortColumnsByLayout
} from '../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/sort-columns-by-layout'

const field = (attribute: string): TreeNode => ({ key: attribute, attribute, isFieldDefinition: true })

const objectColumns = (localizedfields: TreeNode): TreeNode[] => [
  {
    key: 'object-columns',
    children: [
      {
        key: 'panel',
        children: [
          field('name'),
          localizedfields,
          field('price')
        ]
      }
    ]
  },
  { key: 'system-columns', children: [field('id')] },
  { key: 'brick-container-bricks', children: [{ key: 'brick-group', children: [field('Engine~power')] }] }
]

const title = field('title')

// 2026.x: localizedfields is a field definition itself, so its children are not walked
const currentTree = objectColumns({ key: 'localizedfields', attribute: 'localizedfields', isFieldDefinition: true, children: [title] })

// After #1199: localizedfields is a container, so its fields are walked
const containerTree = objectColumns({ key: 'localizedfields', attribute: 'localizedfields', isFieldDefinition: false, children: [title] })

const column = (attribute: string): { isOperator: boolean, attributes: { attribute?: string, label?: string } } =>
  ({ isOperator: false, attributes: { attribute } })
const operator = (label: string): { isOperator: boolean, attributes: { attribute?: string, label?: string } } =>
  ({ isOperator: true, attributes: { label } })
const attributesOf = (columns: Array<ReturnType<typeof column>>): Array<string | undefined> =>
  columns.map(c => c.attributes.attribute ?? c.attributes.label)

describe('sort columns by layout', () => {
  describe('localizedfields as a field definition (2026.x)', () => {
    test('lists localizedfields itself, not the fields inside it', () => {
      expect(collectAttributeOrder(currentTree)).toEqual(['name', 'localizedfields', 'price', 'id', 'Engine~power'])
    })

    test('a localized column keeps its slot', () => {
      const columns = [column('title'), column('price'), column('name')]
      expect(attributesOf(sortColumnsByLayout(columns, collectAttributeOrder(currentTree)))).toEqual(['title', 'name', 'price'])
    })
  })

  describe('localizedfields as a container (after #1199)', () => {
    test('lists the attributes in tree order, through panels and containers', () => {
      expect(collectAttributeOrder(containerTree)).toEqual(['name', 'title', 'price', 'id', 'Engine~power'])
    })

    test('sorts attribute columns like the class layout', () => {
      const columns = [column('Engine~power'), column('id'), column('price'), column('name'), column('title')]
      expect(attributesOf(sortColumnsByLayout(columns, collectAttributeOrder(containerTree)))).toEqual(['name', 'title', 'price', 'id', 'Engine~power'])
    })
  })

  test('keeps operators and unknown attributes at their index', () => {
    const columns = [column('price'), operator('Concat'), column('name'), column('removedField')]
    expect(attributesOf(sortColumnsByLayout(columns, collectAttributeOrder(containerTree)))).toEqual(['name', 'Concat', 'price', 'removedField'])
  })

  test('returns the columns unchanged when they are already sorted', () => {
    const columns = [column('name'), column('price')]
    expect(sortColumnsByLayout(columns, collectAttributeOrder(containerTree))).toEqual(columns)
  })
})
