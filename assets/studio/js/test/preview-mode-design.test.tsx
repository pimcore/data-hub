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

const turnOn = (): void => { fireEvent.click(screen.getByRole('switch')) }

beforeEach(() => {
  resetApiState()
  apiState.available = AVAILABLE
  apiState.grid = (arg) => isPreselectRequest(arg)
    ? { data: FIRST_OBJECT }
    : { data: { items: [{ id: 5, columns: [{ key: 'name', locale: 'en', value: 'Sneaker' }, { key: 'sku', value: 'S-1' }] }] } }
  apiState.gridPreview = () => ({ data: { value: [{ type: 'string', value: 'Calculated' }] } })
})

describe('schema modal preview mode design', () => {
  test('AC1: the switch, the bar and the language switch are in one row, followed by a divider', () => {
    render(modal())
    turnOn()

    const header = screen.getByTestId('preview-mode-header')
    expect(within(header).getByRole('switch')).toBeInTheDocument()
    expect(within(header).getByText('preview-mode.preview-item')).toBeInTheDocument()
    expect(within(header).getByText('ID: 5')).toBeInTheDocument()
    expect(within(header).getByLabelText('preview-language')).toBeInTheDocument()
    expect(screen.getAllByTestId('preview-bar')).toHaveLength(1)
    expect(screen.getByTestId('preview-mode-header-divider')).toBeInTheDocument()
  })

  test('AC1: the language of the bar decides the language of the values', () => {
    render(modal())
    turnOn()
    fireEvent.change(screen.getByLabelText('preview-language'), { target: { value: 'de' } })

    const last = apiState.calls.grid.filter(isSimpleRequest).slice(-1)[0]
    expect(last.body.columns.find((column: any) => column.key === 'name').locale).toBe('de')
  })

  test('AC2: every column is a card with a divider and a "Preview" row holding its value', () => {
    render(modal())
    turnOn()

    expect(screen.getAllByTestId('preview-mode-column-preview-divider')).toHaveLength(3)
    const rows = screen.getAllByTestId('preview-mode-column-preview-row')
    expect(rows).toHaveLength(3)
    rows.forEach(row => { expect(within(row).getByText('preview-mode.preview')).toBeInTheDocument() })
    expect(within(rows[0]).getByText(/"Sneaker"/)).toBeInTheDocument()
    expect(within(rows[2]).getByText(/Calculated/)).toBeInTheDocument()
    expect(screen.queryByText(/grid\.advanced-column\.preview/)).not.toBeInTheDocument()
  })

  test('AC2: loading, empty and error states sit in the "Preview" row', () => {
    apiState.gridPreview = () => ({ error: { error: 'Boom' } })
    apiState.grid = (arg) => isSimpleRequest(arg) ? { data: { items: [{ id: 5, columns: [] }] } } : { data: FIRST_OBJECT }
    render(modal())
    turnOn()

    const rows = screen.getAllByTestId('preview-mode-column-preview-row')
    expect(within(rows[0]).getByText('data-hub.column-config-modal.preview.no-data')).toBeInTheDocument()
    expect(within(rows[2]).getByText('Boom')).toBeInTheDocument()
  })

  test('AC3: with preview mode off there is only the switch, no divider, no "Preview" row and no request', () => {
    render(modal())

    expect(screen.getByRole('switch')).toBeInTheDocument()
    expect(screen.queryByTestId('preview-bar')).not.toBeInTheDocument()
    expect(screen.queryByTestId('preview-mode-header-divider')).not.toBeInTheDocument()
    expect(screen.queryByTestId('preview-mode-column-preview-divider')).not.toBeInTheDocument()
    expect(screen.queryByTestId('preview-mode-column-preview-row')).not.toBeInTheDocument()
    expect(apiState.calls.grid).toHaveLength(0)
    expect(apiState.calls.gridPreview).toHaveLength(0)
  })

  test('AC4: the bar and the "Preview" rows come from one switch, F6 behaviour stays', () => {
    render(modal())
    expect(screen.queryByTestId('preview-bar')).not.toBeInTheDocument()
    turnOn()
    expect(screen.getAllByTestId('preview-bar')).toHaveLength(1)
    expect(screen.getAllByTestId('preview-mode-column-preview-row')).toHaveLength(3)
  })

  test('AC5: the state of the screenshots renders: bar, divider and a "Preview" row per column', () => {
    render(modal())
    turnOn()
    expect(screen.getByTestId('preview-mode-header-divider')).toBeInTheDocument()
    expect(screen.getAllByTestId('preview-mode-column-preview-divider')).toHaveLength(3)
  })
})
