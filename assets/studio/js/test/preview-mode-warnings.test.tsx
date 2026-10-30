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
import { render, screen, fireEvent } from '@testing-library/react'
import { apiState, resetApiState } from './mocks/studio-ui-bundle/api-state'
import { ColumnConfigModal } from '../src/modules/config/components/column-config-modal/column-config-modal'
import { BaseColumnEditor } from '../src/modules/config/components/column-config-modal/base-column-editor'

jest.mock('../src/modules/config/components/column-config-modal/base-column-editor.styles', () => ({
  useStyles: () => ({ styles: {} })
}))

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

// The shared mock's layout drops the toolbar; the Save button lives in it.
jest.mock('@pimcore/studio-ui-bundle/components', () => ({
  ...jest.requireActual('./mocks/studio-ui-bundle/components'),
  ContentLayout: ({ renderToolbar, children }: any) => <div>{ renderToolbar }{ children }</div>
}))

const AVAILABLE = {
  data: {
    columns: [
      { key: 'name', type: 'dataobject.adapter', localizable: true, group: ['g'], config: { fieldDefinition: { fieldtype: 'input' } } },
      { key: 'advanced', type: 'dataobject.advanced', localizable: true, config: {} }
    ]
  },
  isLoading: false
}

// The empty-key column is the one thing the modal's own validation drops when saving.
const COLUMNS = [
  { key: 'name', fieldtype: 'input', type: 'dataobject.adapter' },
  { key: '', fieldtype: 'input', type: 'dataobject.adapter' },
  { key: 'Calc', fieldtype: 'advanced', type: 'dataobject.advanced', config: { advancedColumns: [], transformers: [] } }
]

const isSimpleRequest = (arg: any): boolean => arg.body.filters.columnFilters !== undefined

const modal = (onApply: (columns: any[]) => void = () => {}): React.JSX.Element => (
  <ColumnConfigModal
    columns={ COLUMNS }
    entity='Product'
    onApply={ onApply }
    onCancel={ () => {} }
    open
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

const PREVIEW_ERROR = 'data-hub.column-config-modal.preview.error'

// The list of columns without the preview rows: what must not change when preview mode is switched.
const columnList = (): string[] =>
  screen.getAllByRole('listitem').map(item => {
    const clone = item.cloneNode(true) as HTMLElement
    clone.querySelectorAll('[data-testid^="preview-mode-column-preview"]').forEach(node => { node.remove() })
    return clone.innerHTML
  })

beforeEach(() => {
  resetApiState()
  apiState.available = AVAILABLE
  apiState.grid = (arg) => isSimpleRequest(arg)
    ? { error: { status: 500 } }
    : { data: { items: [{ id: 5, columns: [{ key: 'fullpath', value: '/P/S' }] }] } }
  apiState.gridPreview = () => ({ error: { error: 'Boom' } })
})

describe('schema modal warnings and errors with preview mode on and off', () => {
  test('AC1: the modal shows no warning or error element of its own, with preview on or off', () => {
    render(modal())
    expect(screen.queryByRole('alert')).not.toBeInTheDocument()
    expect(screen.queryByText(PREVIEW_ERROR)).not.toBeInTheDocument()
    expect(screen.queryByText('Boom')).not.toBeInTheDocument()

    fireEvent.click(screen.getByRole('switch'))
    expect(screen.queryByRole('alert')).not.toBeInTheDocument()
  })

  test('AC1: the preview error is preview-only and the column list is otherwise identical on and off', () => {
    render(modal())
    const off = columnList()

    fireEvent.click(screen.getByRole('switch'))
    expect(screen.getAllByText(PREVIEW_ERROR).length).toBeGreaterThan(0)
    expect(screen.getByText('Boom')).toBeInTheDocument()
    expect(columnList()).toEqual(off)

    fireEvent.click(screen.getByRole('switch'))
    expect(screen.queryByText(PREVIEW_ERROR)).not.toBeInTheDocument()
    expect(screen.queryByText('Boom')).not.toBeInTheDocument()
    expect(columnList()).toEqual(off)
  })

  test('AC2: Save sends the same configuration with preview on and off, the empty column dropped in both', () => {
    const off = jest.fn()
    const { unmount } = render(modal(off))
    fireEvent.click(screen.getByText('data-hub.column-config-modal.apply'))
    unmount()

    const on = jest.fn()
    render(modal(on))
    fireEvent.click(screen.getByRole('switch'))
    fireEvent.click(screen.getByText('data-hub.column-config-modal.apply'))

    expect(off).toHaveBeenCalledTimes(1)
    expect(on).toHaveBeenCalledTimes(1)
    expect(on.mock.calls[0][0]).toEqual(off.mock.calls[0][0])
    expect(off.mock.calls[0][0].map((column: any) => column.key)).toEqual(['name', 'Calc'])
  })
})
