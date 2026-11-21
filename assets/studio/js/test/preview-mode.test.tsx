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
import { render, screen, fireEvent, within } from '@testing-library/react'
import pkg from '../../package.json'
import { apiState, resetApiState } from './mocks/studio-ui-bundle/api-state'
import { ColumnConfigModal } from '../src/modules/config/components/column-config-modal/column-config-modal'
import { BaseColumnEditor } from '../src/modules/config/components/column-config-modal/base-column-editor'

jest.mock('../src/modules/config/components/column-config-modal/base-column-editor.styles', () => ({
  useStyles: () => ({ styles: {} })
}))

// The pipeline form is the SDK's pipeline UI; only its preview slot matters here.
jest.mock('../src/modules/config/components/column-config-modal/column-pipeline-form', () => {
  const { ColumnPreview } = jest.requireActual('../src/modules/config/components/column-config-modal/column-preview')
  return {
    ColumnPipelineForm: ({ column, value }: any) => (
      <ColumnPreview
        column={ column }
        pipelineValue={ value }
      />
    )
  }
})

const AVAILABLE = {
  data: {
    columns: [
      { key: 'name', type: 'dataobject.adapter', localizable: true, group: ['g'], config: { fieldDefinition: { fieldtype: 'input' } } },
      { key: 'sku', type: 'dataobject.adapter', localizable: false, group: ['g'], config: { fieldDefinition: { fieldtype: 'input' } } },
      { key: 'advanced', type: 'dataobject.advanced', localizable: true, config: {} }
    ]
  },
  isLoading: false
}

const COLUMNS = [
  { key: 'name', fieldtype: 'input', type: 'dataobject.adapter' },
  { key: 'sku', fieldtype: 'input', type: 'dataobject.adapter' },
  { key: 'Calc', fieldtype: 'advanced', type: 'dataobject.advanced', config: { advancedColumns: [], transformers: [] } }
]

const FIRST_OBJECT = { items: [{ id: 5, columns: [{ key: 'fullpath', value: '/Products/Shoes/Sneaker' }] }] }

const isSimpleRequest = (arg: any): boolean => arg.body.filters.columnFilters !== undefined
const isPreselectRequest = (arg: any): boolean => arg.body.filters.columnFilters === undefined

interface Options { columns?: any[], columnConfig?: Record<string, unknown>, open?: boolean }

const modal = ({ columns = COLUMNS, columnConfig, open = true }: Options = {}): React.JSX.Element => (
  <ColumnConfigModal
    columnConfig={ columnConfig }
    columns={ columns }
    entity='Product'
    onApply={ () => {} }
    onCancel={ () => {} }
    open={ open }
    renderEditor={ (props) => (
      <BaseColumnEditor
        { ...props }
        sourceFieldsRegistryId='source'
        transformersRegistryId='transformers'
      />
    ) }
    title='Schema'
  />
)

const advancedItem = (): HTMLElement => screen.getByText('Calc').closest('li') as HTMLElement

const turnOn = (): void => { fireEvent.click(screen.getByRole('switch')) }

beforeEach(() => {
  resetApiState()
  apiState.available = AVAILABLE
  apiState.grid = (arg) => isPreselectRequest(arg) ? { data: FIRST_OBJECT } : {}
})

describe('schema modal preview mode', () => {
  test('AC1: the switch sits below the title and is off on every open', () => {
    const { rerender } = render(modal())
    expect(screen.getByRole('switch')).not.toBeChecked()

    turnOn()
    expect(screen.getByRole('switch')).toBeChecked()

    rerender(modal({ open: false }))
    rerender(modal({ open: true }))
    expect(screen.getByRole('switch')).not.toBeChecked()
  })

  test('AC2: with preview mode off there is no bar, no preview and no preview request', () => {
    render(modal())

    expect(screen.queryByText('preview-mode.preview-item')).not.toBeInTheDocument()
    expect(screen.queryByText('data-hub.column-config-modal.preview.select-object')).not.toBeInTheDocument()
    expect(screen.getByLabelText('preview-language')).toBeInTheDocument()
    expect(screen.queryByTestId('preview-grid')).not.toBeInTheDocument()
    expect(apiState.calls.grid).toHaveLength(0)
    expect(apiState.calls.gridPreview).toHaveLength(0)
  })

  test('AC3: with preview mode on the bar offers "Preview item" for objects of the modal class, next to the language select', () => {
    render(modal())
    turnOn()

    const bar = screen.getByTestId('preview-bar')
    expect(bar).toHaveAttribute('data-classes', 'Product')
    expect(within(bar).getByText('preview-mode.preview-item')).toBeInTheDocument()
    expect(screen.getByLabelText('preview-language')).toBeInTheDocument()
  })

  test('AC4: the first object is preselected, picking replaces it, and an empty class shows no item', () => {
    render(modal())
    turnOn()
    expect(screen.getByText('ID: 5')).toBeInTheDocument()
    expect(apiState.calls.grid.filter(isPreselectRequest)[0]).toMatchObject({ classId: 'class-Product' })

    fireEvent.click(screen.getByText('preview-mode.preview-item'))
    expect(screen.getByText('ID: 9')).toBeInTheDocument()
    expect(screen.queryByText('ID: 5')).not.toBeInTheDocument()
  })

  test('AC4: shows "no preview item" when the class has no object', () => {
    apiState.grid = () => ({ data: { items: [] } })
    render(modal())
    turnOn()

    expect(screen.getByText('preview-mode.no-item')).toBeInTheDocument()
    expect(screen.getAllByText('data-hub.column-config-modal.preview.no-item').length).toBeGreaterThan(0)
  })

  test('AC5: advanced columns show their value for the item in the preview language', () => {
    apiState.gridPreview = () => ({ data: { value: [{ type: 'string', value: 'Calculated' }] } })
    render(modal())
    turnOn()

    const request = apiState.calls.gridPreview[0]
    expect(request.body.objectId).toBe(5)
    expect(request.body.column).toMatchObject({ key: 'Calc', type: 'dataobject.advanced' })
    expect(within(advancedItem()).getByText(/Calculated/)).toBeInTheDocument()
  })

  test('AC5: advanced columns show loading, error and empty states', () => {
    apiState.gridPreview = () => ({ isFetching: true })
    const { unmount } = render(modal())
    turnOn()
    expect(within(advancedItem()).getByText('data-hub.column-config-modal.preview.loading')).toBeInTheDocument()
    unmount()

    apiState.gridPreview = () => ({ error: { error: 'Boom' } })
    const second = render(modal())
    turnOn()
    expect(within(advancedItem()).getByText('Boom')).toBeInTheDocument()
    second.unmount()

    apiState.gridPreview = () => ({ data: { value: [] } })
    render(modal())
    turnOn()
    expect(within(advancedItem()).getByText('data-hub.column-config-modal.preview.no-data')).toBeInTheDocument()
  })

  test('AC6: simple columns are loaded with one grid request for all of them', () => {
    apiState.grid = (arg) => isSimpleRequest(arg)
      ? { data: { items: [{ id: 5, columns: [{ key: 'name', locale: 'en', value: 'Sneaker' }, { key: 'sku', value: 'S-1' }] }] } }
      : { data: FIRST_OBJECT }
    render(modal())
    turnOn()

    const requests = apiState.calls.grid.filter(isSimpleRequest)
    expect(requests.length).toBeGreaterThan(0)
    requests.forEach((request) => {
      expect(request.body.columns.map((column: any) => column.key)).toEqual(['name', 'sku'])
      expect(request.body.filters.columnFilters).toEqual([{ type: 'system.ids', filterValue: [5] }])
    })
    expect(screen.getByText(/dataobject.adapter:input="Sneaker"/)).toBeInTheDocument()
    expect(screen.getByText(/dataobject.adapter:input="S-1"/)).toBeInTheDocument()
  })

  test('AC6: simple columns show error and empty states', () => {
    apiState.grid = (arg) => isSimpleRequest(arg) ? { error: { status: 500 } } : { data: FIRST_OBJECT }
    const { unmount } = render(modal())
    turnOn()
    expect(screen.getAllByText('data-hub.column-config-modal.preview.error')).toHaveLength(2)
    unmount()

    apiState.grid = (arg) => isSimpleRequest(arg) ? { data: { items: [{ id: 5, columns: [] }] } } : { data: FIRST_OBJECT }
    render(modal())
    turnOn()
    expect(screen.getAllByText('data-hub.column-config-modal.preview.no-data').length).toBeGreaterThanOrEqual(2)
  })

  test('AC7: the migration (split) view has the same switch, off on open, and the same bar', () => {
    render(modal({ columnConfig: { legacy: true } }))
    expect(screen.queryByRole('switch')).not.toBeInTheDocument()

    fireEvent.click(screen.getByText('data-hub.migration-modal.start-migration'))
    expect(screen.getByRole('switch')).not.toBeChecked()
    expect(screen.queryByText('preview-mode.preview-item')).not.toBeInTheDocument()

    turnOn()
    expect(screen.getByTestId('preview-bar')).toHaveAttribute('data-classes', 'Product')
    expect(screen.getByText('ID: 5')).toBeInTheDocument()
  })

  test('AC8: npm test runs Jest', () => {
    expect(pkg.scripts.test.split(' && ')[0]).toBe('jest')
    expect(pkg.scripts['check-types']).toBeDefined()
    expect(pkg.scripts.lint).toBeDefined()
  })
})
