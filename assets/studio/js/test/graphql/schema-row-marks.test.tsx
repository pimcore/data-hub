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
import { cleanup, fireEvent, render, screen, within } from '@testing-library/react'
import type { ChangeSetReviewData } from '@pimcore/change-control-bundle/sdk'

vi.mock('@pimcore/studio-ui-bundle/app', () => ({
  useTranslation: () => ({ t: (key: string) => key })
}))

// a grid that renders each row's cells, and the operations slot as the accordion's header button
vi.mock('@pimcore/studio-ui-bundle/components', () => {
  const OperationalGrid = ({ columns, value, children }: { columns: any[], value: any[], children?: React.ReactNode }): React.JSX.Element => (
    <div>
      <table>
        <tbody>
          { value.map((row, index) => (
            <tr
              data-testid={ `row-${row.entity as string}` }
              key={ row.id }
            >
              { columns.map((column) => (
                <td key={ column.id ?? column.accessorKey }>
                  { column.cell !== undefined ? column.cell({ row: { index } }) : String(row[column.accessorKey]) }
                </td>
              )) }
            </tr>
          )) }
        </tbody>
      </table>
      { children }
    </div>
  )
  OperationalGrid.Operations = function Operations ({ children }: { children: () => React.ReactNode }): React.JSX.Element {
    return <>{ children() }</>
  }
  OperationalGrid.Grid = function Grid (): null {
    return null
  }
  return {
    Flex: ({ children }: { children?: React.ReactNode }) => <div>{ children }</div>,
    IconButton: ({ icon, onClick }: { icon: { value: string }, onClick: () => void }) => <button onClick={ onClick }>{ icon.value }</button>,
    IconTextButton: ({ children, onClick }: { children?: React.ReactNode, onClick: (event: { stopPropagation: () => void }) => void }) => (
      <button onClick={ () => { onClick({ stopPropagation: () => undefined }) } }>{ children }</button>
    ),
    Accordion: ({ items }: { items: Array<{ key: string, info?: React.ReactNode, children?: React.ReactNode }> }) => (
      <div>{ items.map((item) => <div key={ item.key }>{ item.info }{ item.children }</div>) }</div>
    ),
    Modal: () => null,
    Select: () => null,
    OperationalGrid
  }
})

vi.mock('@pimcore/studio-ui-bundle/modules/data-object', () => ({
  useClassDefinitions: () => ({ getAllClassDefinitions: () => [] })
}))

vi.mock('../../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal', () => ({
  SchemaFieldsModal: ({ className, disabled }: { className: string, disabled: boolean }) => (
    <div
      data-disabled={ String(disabled) }
      data-testid="fields-modal"
    >{ className }</div>
  )
}))

const { QueryGrid } = await import('../../src/modules/graphql/components/tabs/schema-definition-tab/query-grid')
const { MutationGrid } = await import('../../src/modules/graphql/components/tabs/schema-definition-tab/mutation-grid')
const { SchemaRowMarksContext } = await import('../../src/modules/graphql/components/tabs/schema-definition-tab/row-marks')
const { configChanges, schemaRowMarks } = await import('../../src/modules/graphql/change-control/config-review-model')

// Car gains a column; Manufacturer is dropped
const PAYLOAD = {
  subjectType: 'data-hub-graphql-config',
  subjectRef: 'shop',
  slots: {
    schema: {
      shape: 'record',
      current: {
        'schema.queryEntities.Car.id': 'Car',
        'schema.queryEntities.Car.columns': [1, 2, 3, 4, 5],
        'schema.queryEntities.Manufacturer.id': 'Manufacturer',
        'schema.queryEntities.Manufacturer.name': 'Manufacturer'
      },
      proposed: {
        'schema.queryEntities.Car.id': 'Car',
        'schema.queryEntities.Car.columns': [1, 2, 3, 4, 5, 6],
        'schema.queryEntities.Manufacturer': null,
        'schema.mutationEntities.Car.id': 'Car',
        'schema.mutationEntities.Car.name': 'Car'
      }
    }
  }
}

afterEach(() => { cleanup() })

describe('schema grid rows in a review', () => {
  it('derives a mark per entity: a change inside it, or the entity added or removed whole', () => {
    expect(schemaRowMarks(configChanges(PAYLOAD as unknown as ChangeSetReviewData))).toEqual({
      query: { Car: 'changed', Manufacturer: 'removed' },
      mutation: { Car: 'added' }
    })
  })

  it('marks the changed row, lists the removed entity, keeps the gear and drops trash and add', () => {
    render(
      <SchemaRowMarksContext.Provider value={ { query: { Car: 'changed', Manufacturer: 'removed' } } }>
        <QueryGrid
          isWriteable={ false }
          value={ [{ id: 'Car', entity: 'Car' }, { id: 'Product', entity: 'Product' }] }
        />
      </SchemaRowMarksContext.Provider>
    )

    expect(within(screen.getByTestId('row-Car')).getByTestId('row-mark-changed').textContent).toBe('form.annotation.changed')
    expect(within(screen.getByTestId('row-Product')).queryByTestId(/row-mark-/)).toBeNull()
    expect(within(screen.getByTestId('row-Manufacturer')).getByTestId('row-mark-removed')).toBeTruthy()

    expect(screen.queryByText('trash')).toBeNull()
    expect(screen.queryByText('add')).toBeNull()
    // a removed entity has no fields left to show
    expect(within(screen.getByTestId('row-Manufacturer')).queryByText('settings')).toBeNull()

    fireEvent.click(within(screen.getByTestId('row-Car')).getByText('settings'))
    expect(screen.getByTestId('fields-modal').textContent).toBe('Car')
    expect(screen.getByTestId('fields-modal').dataset.disabled).toBe('true')
  })

  it('marks mutation rows the same way', () => {
    render(
      <SchemaRowMarksContext.Provider value={ { mutation: { Car: 'added' } } }>
        <MutationGrid
          isWriteable={ false }
          value={ [{ id: 'Car', entity: 'Car', create: false, update: true, delete: false }] }
        />
      </SchemaRowMarksContext.Provider>
    )

    expect(within(screen.getByTestId('row-Car')).getByTestId('row-mark-added')).toBeTruthy()
    expect(screen.queryByText('trash')).toBeNull()
  })

  it('stays as it was outside a review: no marks, trash and add offered', () => {
    render(<QueryGrid value={ [{ id: 'Car', entity: 'Car' }] } />)

    expect(screen.queryByTestId(/row-mark-/)).toBeNull()
    expect(screen.getByText('trash')).toBeTruthy()
    expect(screen.getByText('add')).toBeTruthy()
  })
})
