/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

// @vitest-environment jsdom

import React from 'react'
import { afterEach, describe, expect, it, vi } from 'vitest'
import { cleanup, render, screen, within } from '@testing-library/react'
import type { ChangeSetReviewData } from '@pimcore/change-control-bundle/sdk'
import type { PersistedColumnConfig } from '../../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/types'

vi.mock('@pimcore/studio-ui-bundle/app', () => ({
  useTranslation: () => ({ t: (key: string) => key }),
  useInjection: () => ({}),
  serviceIds: {}
}))

vi.mock('@pimcore/studio-ui-bundle/utils', () => ({
  isNonEmptyString: (value: unknown) => typeof value === 'string' && value !== '',
  uuid: () => Math.random().toString(36).slice(2)
}))

// a tree that renders each node through the tree's titleRender, and the modal's frame around its panes
vi.mock('@pimcore/studio-ui-bundle/components', () => {
  interface Node { key: string, title: React.ReactNode, children?: Node[] }
  const nodes = (list: Node[], titleRender: (node: Node, initial: React.ReactNode) => React.ReactNode): React.ReactNode =>
    list.map((node) => (
      <div
        data-testid={ `node-${node.key}` }
        key={ node.key }
      >
        { titleRender(node, <span>{ node.title }</span>) }
        { nodes(node.children ?? [], titleRender) }
      </div>
    ))
  const Pass = ({ children }: { children?: React.ReactNode }): React.JSX.Element => <div>{ children }</div>
  return {
    Flex: Pass,
    Content: Pass,
    Title: Pass,
    SidebarProvider: Pass,
    Sidebar: () => null,
    Button: Pass,
    TreeElement: ({ treeData, titleRender }: { treeData: Node[], titleRender: (node: Node, initial: React.ReactNode) => React.ReactNode }) => (
      <div>{ nodes(treeData, titleRender) }</div>
    ),
    Modal: ({ open, children }: { open: boolean, children?: React.ReactNode }) => open ? <div>{ children }</div> : null,
    SplitLayout: ({ leftItem, rightItem }: { leftItem: { children: React.ReactNode }, rightItem: { children: React.ReactNode } }) => (
      <div>{ leftItem.children }{ rightItem.children }</div>
    ),
    // one instance, as the host's form is: a new one each render would reload the entity in a loop
    Form: { useFormInstance: () => FORM }
  }
})

const FORM = { getFieldValue: () => [{ id: 'Car', entity: 'Car', columnConfig: { columns: CAR_COLUMNS } }] }

vi.mock('@pimcore/studio-ui-bundle/modules/data-object', () => ({
  useClassDefinitions: () => ({ getByName: () => ({ id: 'CAR' }) })
}))

vi.mock('../../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/hooks/use-tree-nodes', () => ({
  useTreeNodes: ({ items }: { items: Array<{ key: string, attributes: { label?: string, children?: unknown[] } }> }) => {
    const toNodes = (list: typeof items): unknown[] => list.map((item) => ({
      key: item.key,
      title: item.attributes.label,
      itemData: item,
      children: toNodes((item.attributes.children ?? []) as typeof items)
    }))
    return toNodes(items)
  }
}))

vi.mock('../../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/tree-node-renderer', () => ({
  TreeNodeRenderer: ({ initialComponent }: { initialComponent: React.ReactNode }) => <>{ initialComponent }</>
}))

vi.mock('../../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/empty-tree-drop-zone', () => ({
  EmptyTreeDropZone: () => null
}))

vi.mock('../../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-sidebar-entries', () => ({
  useSidebarEntries: () => []
}))

vi.mock('../../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/add-all-definitions-button', () => ({
  AddAllDefinitionsButton: () => null
}))

const field = (key: string, attribute: string, label = attribute): PersistedColumnConfig => ({
  key, isOperator: false, attributes: { attribute, label, dataType: 'input' }
})
const operator = (key: string, label: string, children: PersistedColumnConfig[]): PersistedColumnConfig => ({
  key, isOperator: true, attributes: { class: 'concatenator', label, dataType: '', children: children as unknown as PersistedColumnConfig['attributes']['children'] }
})

// the proposal: name relabelled, sku unchanged, color new, a concatenator with a child; price is gone
const CAR_COLUMNS = [
  field('k-name', 'name', 'Model name'),
  field('k-sku', 'sku'),
  field('k-color', 'color'),
  operator('k-concat', 'Title', [field('k-child', 'name')])
]
const CAR_BEFORE = [
  field('old-sku', 'sku'),
  field('old-name', 'name'),
  field('old-price', 'price', 'Price'),
  operator('old-concat', 'Title', [field('old-child', 'name')])
]

const { fieldMarks } = await import('../../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/field-marks')
const { AvailableFieldsTree } = await import('../../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/available-fields-tree')
const { SchemaFieldsModal } = await import('../../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal')
const { SchemaPreviousColumnsContext } = await import('../../src/modules/graphql/components/tabs/schema-definition-tab/row-marks')
const { configChanges } = await import('../../src/modules/graphql/change-control/config-review-model')
const { schemaPreviousColumns } = await import('../../src/modules/graphql/change-control/schema-previous-columns')

afterEach(() => { cleanup() })

describe('the fields of a schema entity in a review', () => {
  it("takes each changed entity's fields before the change from the change set", () => {
    const changes = configChanges({
      subjectType: 'data-hub-graphql-config',
      subjectRef: 'shop',
      slots: {
        schema: {
          shape: 'record',
          current: {
            'schema.queryEntities.Car.id': 'Car',
            'schema.queryEntities.Car.columnConfig.columns': CAR_BEFORE,
            'schema.queryEntities.Manufacturer.id': 'Manufacturer'
          },
          proposed: {
            'schema.queryEntities.Car.id': 'Car',
            'schema.queryEntities.Car.columnConfig.columns': CAR_COLUMNS,
            'schema.queryEntities.Manufacturer.id': 'Manufacturer',
            'schema.mutationEntities.Car.columnConfig.columns': [field('m-name', 'name')]
          }
        }
      }
    } as unknown as ChangeSetReviewData)

    expect(schemaPreviousColumns(changes)).toEqual({
      query: { Car: CAR_BEFORE },
      // an entity the change adds had no fields before
      mutation: { Car: [] }
    })
  })

  it('pairs the fields by attribute or operator class: changed, added, unchanged and removed', () => {
    expect(fieldMarks(CAR_COLUMNS, CAR_BEFORE)).toEqual({
      marks: ['changed', undefined, 'added', undefined],
      removed: [CAR_BEFORE[2]]
    })
  })

  it('pairs each previous field once, so a duplicate added beside an unchanged one is added', () => {
    expect(fieldMarks([field('a', 'sku'), field('b', 'sku')], [field('x', 'sku')]).marks).toEqual([undefined, 'added'])
  })

  it('marks nothing without previous fields', () => {
    expect(fieldMarks(CAR_COLUMNS)).toEqual({ marks: [undefined, undefined, undefined, undefined], removed: [] })
  })

  it('shows the marks on the top-level fields and lists the removed ones', () => {
    render(
      <AvailableFieldsTree
        disabled
        entityConfig={ { id: 'Car', name: 'Car', columnConfig: { columns: CAR_COLUMNS } } }
        fieldMarks={ fieldMarks(CAR_COLUMNS, CAR_BEFORE) }
        onEntityConfigChange={ () => undefined }
        operatorRegistryServiceId="operators"
      />
    )

    expect(within(screen.getByTestId('node-k-name')).getAllByTestId('row-mark-changed')).toHaveLength(1)
    expect(within(screen.getByTestId('node-k-color')).getByTestId('row-mark-added')).toBeTruthy()
    expect(within(screen.getByTestId('node-k-sku')).queryByTestId(/row-mark-/)).toBeNull()
    // an operator's child is not paired, so it carries no mark of its own
    expect(within(screen.getByTestId('node-k-child')).queryByTestId(/row-mark-/)).toBeNull()
    expect(screen.getByTestId('removed-field').textContent).toBe('Price')
    expect(screen.getAllByTestId('row-mark-removed')).toHaveLength(1)
  })

  it('lists every removed field, also the ones stored without a key', () => {
    const { key: _price, ...price } = field('old-price', 'price', 'Price')
    const { key: _stock, ...stock } = field('old-stock', 'stock', 'Stock')
    const before = [...CAR_BEFORE.filter((column) => column.key !== 'old-price'), price, stock]
    const error = vi.spyOn(console, 'error').mockImplementation(() => undefined)

    render(
      <AvailableFieldsTree
        disabled
        entityConfig={ { id: 'Car', name: 'Car', columnConfig: { columns: CAR_COLUMNS } } }
        fieldMarks={ fieldMarks(CAR_COLUMNS, before) }
        onEntityConfigChange={ () => undefined }
        operatorRegistryServiceId="operators"
      />
    )

    expect(screen.getAllByTestId('removed-field').map((node) => node.textContent)).toEqual(['Price', 'Stock'])
    // no duplicate or missing React key among the removed rows
    expect(error.mock.calls.filter(([message]) => String(message).includes('key'))).toEqual([])
    error.mockRestore()
  })

  it('lists removed fields that share an identity, each under its own React key', () => {
    const { key: _first, ...keyless } = field('old-price', 'price', 'Price')
    const before = [...CAR_BEFORE, keyless, { ...keyless }, field('dup', 'stock', 'Stock'), field('dup', 'stock', 'Stock')]
    const error = vi.spyOn(console, 'error').mockImplementation(() => undefined)

    render(
      <AvailableFieldsTree
        disabled
        entityConfig={ { id: 'Car', name: 'Car', columnConfig: { columns: CAR_COLUMNS } } }
        fieldMarks={ fieldMarks(CAR_COLUMNS, before) }
        onEntityConfigChange={ () => undefined }
        operatorRegistryServiceId="operators"
      />
    )

    expect(screen.getAllByTestId('removed-field').map((node) => node.textContent)).toEqual(['Price', 'Price', 'Price', 'Stock', 'Stock'])
    expect(error.mock.calls.filter(([message]) => String(message).includes('key'))).toEqual([])
    error.mockRestore()
  })

  it('marks the fields when the modal opens view-only in a review', () => {
    render(
      <SchemaPreviousColumnsContext.Provider value={ { query: { Car: CAR_BEFORE } } }>
        <SchemaFieldsModal
          className="Car"
          disabled
          onApply={ () => undefined }
          onCancel={ () => undefined }
          open
          operatorRegistryServiceId="operators"
        />
      </SchemaPreviousColumnsContext.Provider>
    )

    expect(within(screen.getByTestId('node-k-color')).getByTestId('row-mark-added')).toBeTruthy()
    expect(screen.getByTestId('removed-field').textContent).toBe('Price')
  })

  it('marks nothing in the editable modal or outside a review', () => {
    render(
      <SchemaPreviousColumnsContext.Provider value={ { query: { Car: CAR_BEFORE } } }>
        <SchemaFieldsModal
          className="Car"
          onApply={ () => undefined }
          onCancel={ () => undefined }
          open
          operatorRegistryServiceId="operators"
        />
      </SchemaPreviousColumnsContext.Provider>
    )
    expect(screen.getByTestId('node-k-color')).toBeTruthy()
    expect(screen.queryByTestId(/row-mark-/)).toBeNull()
    cleanup()

    render(
      <SchemaFieldsModal
        className="Car"
        disabled
        onApply={ () => undefined }
        onCancel={ () => undefined }
        open
        operatorRegistryServiceId="operators"
      />
    )
    expect(screen.getByTestId('node-k-color')).toBeTruthy()
    expect(screen.queryByTestId(/row-mark-/)).toBeNull()
    expect(screen.queryByTestId('removed-field')).toBeNull()
  })
})
