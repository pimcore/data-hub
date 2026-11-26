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
  buildAttributeTreeItem,
  collectColumnFieldDefinitions,
  isAttributeContainer
} from '../js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/attribute-container.ts'
import {
  collectLocalizedFieldNames,
  createBrickColumnFactory
} from '../js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/brick-container-helpers.ts'

const DATA_FIELD_TYPES = new Set(['input', 'textarea', 'localizedfields', 'objectbricks', 'block'])

// Mirrors buildTree() of the studio-ui-bundle: the item callback per layout node, the children kept on the item.
const buildNode = (definition, brickColumn) => buildAttributeTreeItem({
  fieldDefinition: definition,
  initialTreeItem: {
    key: definition.name,
    title: definition.title ?? definition.name,
    children: definition.children?.map(child => buildNode(child, brickColumn))
  },
  isDataField: DATA_FIELD_TYPES.has(definition.fieldtype),
  iconProps: { value: definition.fieldtype },
  brickColumn
})

const localizedFields = (...children) => ({ datatype: 'data', fieldtype: 'localizedfields', name: 'localizedfields', children })
const field = (name, fieldtype = 'input') => ({ datatype: 'data', fieldtype, name, title: name.toUpperCase() })
const panel = (...children) => ({ datatype: 'layout', fieldtype: 'panel', name: 'Layout', children })
const block = (name, ...children) => ({ datatype: 'data', fieldtype: 'block', name, title: name.toUpperCase(), children })

const attributes = nodes => collectColumnFieldDefinitions(nodes).map(node => node.attribute)

describe('attribute container', () => {
  it('treats localized fields as a container of columns', () => {
    assert.equal(isAttributeContainer('localizedfields'), true)
  })

  it('treats field types with a column of their own as columns', () => {
    for (const fieldType of ['input', 'block', 'fieldcollections', 'manyToManyObjectRelation', 'objectbricks']) {
      assert.equal(isAttributeContainer(fieldType), false, fieldType)
    }
  })

  it('treats a missing field type as a column', () => {
    assert.equal(isAttributeContainer(undefined), false)
  })
})

describe('class attribute columns', () => {
  const classLayout = panel(field('sku'), localizedFields(field('name'), field('description', 'textarea')))

  it('inserts the localized fields of a class instead of the localizedfields container', () => {
    const tree = [{ key: 'object-columns', children: [buildNode(classLayout)] }]

    assert.deepEqual(attributes(tree), ['sku', 'name', 'description'])
  })

  it('does not offer the localizedfields container as a column', () => {
    const container = buildNode(localizedFields(field('name')))

    assert.equal(container.isFieldDefinition, false)
    assert.equal(container.className, undefined)
    assert.equal(container.children[0].isFieldDefinition, true)
    assert.equal(container.children[0].title, 'NAME (name)')
  })

  it('leaves out the system columns', () => {
    const tree = [
      { key: 'object-columns', children: [buildNode(field('sku'))] },
      { key: 'system-columns', children: [{ key: 'id', attribute: 'id', isFieldDefinition: true }] }
    ]

    assert.deepEqual(attributes(tree), ['sku'])
  })
})

describe('brick attribute columns', () => {
  const brickLayout = panel(field('power'), localizedFields(field('label')))

  // The brick columns as the hook builds them: one factory per brick and objectbricks field, asked per field name.
  const brickTree = (containerName, ambiguousBrickKeys, layout = brickLayout) => {
    const brickColumn = createBrickColumnFactory('Engine', containerName, layout, ambiguousBrickKeys)
    const buildBrickNode = definition => buildAttributeTreeItem({
      fieldDefinition: definition,
      initialTreeItem: { key: definition.name, title: definition.name, children: definition.children?.map(buildBrickNode) },
      isDataField: DATA_FIELD_TYPES.has(definition.fieldtype),
      iconProps: {},
      brickColumn: brickColumn(definition.name)
    })

    return [{ key: `brick-container-${containerName}`, children: layout.children.map(buildBrickNode) }]
  }

  it('finds the fields inside localizedfields only', () => {
    assert.deepEqual([...collectLocalizedFieldNames(brickLayout)], ['label'])
    assert.deepEqual([...collectLocalizedFieldNames({ children: [field('power')] })], [])
  })

  it('does not look into the localizedfields of a block', () => {
    const layout = panel(field('title'), block('variants', localizedFields(field('title'))), localizedFields(block('notes')))

    assert.deepEqual([...collectLocalizedFieldNames(layout)], ['notes'])
  })

  it('addresses localized brick fields through the brick descriptor', () => {
    assert.deepEqual(attributes(brickTree('bricks', new Set())), [
      'Engine~power',
      '?{"containerKey":"Engine","fieldname":"bricks","brickfield":"label"}~label'
    ])
  })

  it('keeps the containing field for localized fields of ambiguous bricks', () => {
    assert.deepEqual(attributes(brickTree('secondary', new Set(['Engine']))), [
      'Engine~power~secondary',
      '?{"containerKey":"Engine","fieldname":"secondary","brickfield":"label"}~label~secondary'
    ])
  })

  it('addresses a brick field by its plain key when a block of the brick has a localized field of the same name', () => {
    const layout = panel(field('title'), block('variants', localizedFields(field('title'))))
    const brickColumn = createBrickColumnFactory('Engine', 'bricks', layout, new Set())

    assert.deepEqual(brickColumn('title')('Title'), { attribute: 'Engine~title', title: 'Title (Engine.title)' })
  })
})
