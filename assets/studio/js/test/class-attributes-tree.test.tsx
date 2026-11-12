/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React from 'react'
import { render, renderHook, screen } from '@testing-library/react'
import { useClassAttributesTree } from '../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-class-attributes-tree'
import { DraggableTreeTitle } from '../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/draggable-tree-title'
import { type SchemaFieldsMode, type TreeNode } from '../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/types'

let classFields: string[] = []

jest.mock('@pimcore/studio-ui-bundle/api/class-definition', () => ({
  useClassDefinitionGetLayoutByIdQuery: () => ({ data: { children: [] }, isLoading: false, isFetching: false })
}), { virtual: true })

// The class layout is reduced to a flat list of input fields named after classFields.
jest.mock('@pimcore/studio-ui-bundle/modules/field-definitions', () => {
  class DynamicTypeFieldDefinitionDataAbstract {
    getIcon (): object { return { value: 'input' } }
  }
  return {
    DynamicTypeFieldDefinitionDataAbstract,
    reduce: () => ({ structure: {}, fieldDefinitions: {} }),
    buildTree: ({ itemCallback }: any) => ({
      children: classFields.map(name => itemCallback({
        fieldDefinition: { name, title: name, fieldtype: 'input' },
        initialTreeItem: { key: `field-${name}`, title: name, isLeaf: true }
      }))
    })
  }
}, { virtual: true })

jest.mock('@pimcore/studio-ui-bundle/app', () => {
  const { DynamicTypeFieldDefinitionDataAbstract } = jest.requireMock('@pimcore/studio-ui-bundle/modules/field-definitions')
  const registry = { getDynamicType: () => new DynamicTypeFieldDefinitionDataAbstract() }
  return {
    serviceIds: { 'DynamicTypes/FieldDefinitionRegistry': 'field-definition-registry' },
    useInjection: () => registry,
    useTranslation: () => ({
      t: (key: string, options?: { title?: string }) => options?.title !== undefined ? `${options.title} - always included` : key
    })
  }
})

jest.mock('@pimcore/studio-ui-bundle/components', () => ({
  ...jest.requireActual('./mocks/studio-ui-bundle/components'),
  Icon: () => <span />,
  Draggable: ({ children }: { children: React.ReactNode }) => <div data-testid='draggable'>{ children }</div>
}))

jest.mock('../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-objectbrick-layouts', () => ({
  useObjectBrickLayouts: () => ({ layouts: new Map(), isLoading: false })
}))

const renderTree = (mode: SchemaFieldsMode): ReturnType<typeof useClassAttributesTree> =>
  renderHook(() => useClassAttributesTree({ classId: '1', enabled: true, mode })).result.current

const systemColumns = (tree: TreeNode[]): TreeNode[] =>
  (tree.find(node => node.key === 'system-columns')?.children ?? []) as TreeNode[]

const insertedAttributes = (mode: SchemaFieldsMode): Array<string | undefined> =>
  renderTree(mode).getFieldDefinitions().map(node => node.attribute)

const ALWAYS_INCLUDED = ['id', 'creationDate', 'modificationDate', 'classname', 'index']

beforeEach(() => {
  classFields = ['name', 'description']
})

describe('getFieldDefinitions ("Insert all definitions")', () => {
  it('adds fullpath, key and published after the class fields in the query schema', () => {
    expect(insertedAttributes('query')).toEqual(['name', 'description', 'fullpath', 'key', 'published'])
  })

  it('adds only the class fields in the mutation schema', () => {
    expect(insertedAttributes('mutation')).toEqual(['name', 'description'])
  })

  it('keeps a class field and skips the system column of the same name', () => {
    classFields = ['name', 'key', 'filename']

    expect(insertedAttributes('query')).toEqual(['name', 'key', 'filename', 'fullpath', 'published'])
  })
})

describe('system columns in the class attributes tree', () => {
  it('disables and labels the always included columns in the query schema', () => {
    const columns = systemColumns(renderTree('query').classAttributesTree)

    expect(columns.filter(node => node.disabled === true).map(node => node.attribute)).toEqual(ALWAYS_INCLUDED)
    expect(columns.find(node => node.attribute === 'id')?.title).toBe('data-hub.schema.system.id - always included')
    expect(columns.find(node => node.attribute === 'key')?.title).toBe('data-hub.schema.system.key')
  })

  it('shows every column enabled and without the label in the mutation schema', () => {
    const columns = systemColumns(renderTree('mutation').classAttributesTree)

    expect(columns.some(node => node.disabled === true)).toBe(false)
    expect(columns.find(node => node.attribute === 'id')?.title).toBe('data-hub.schema.system.id')
  })

  it('makes the disabled nodes not draggable', () => {
    const columns = systemColumns(renderTree('query').classAttributesTree)
    const titleOf = (attribute: string): React.JSX.Element => (
      <DraggableTreeTitle
        initialComponent={ <span>{ attribute }</span> }
        node={ columns.find(node => node.attribute === attribute)! }
      />
    )

    render(<>{ titleOf('id') }{ titleOf('key') }</>)

    expect(screen.getAllByTestId('draggable')).toHaveLength(1)
    expect(screen.getByTestId('draggable')).toHaveTextContent('key')
  })
})
