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
      { key: 'advanced', type: 'dataobject.advanced', localizable: true, config: {} }
    ]
  },
  isLoading: false
}

const COLUMNS = [
  { key: 'name', fieldtype: 'input', type: 'dataobject.adapter' },
  { key: 'Calc', fieldtype: 'advanced', type: 'dataobject.advanced', config: { advancedColumns: [], transformers: [] } }
]

const isSimpleRequest = (arg: any): boolean => arg.body.filters.columnFilters !== undefined

const modal = (language: string | undefined, onLanguageChange: (language: string) => void = () => {}): React.JSX.Element => (
  <ColumnConfigModal
    columns={ COLUMNS }
    entity='Product'
    language={ language }
    onApply={ () => {} }
    onCancel={ () => {} }
    onLanguageChange={ onLanguageChange }
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

beforeEach(() => {
  resetApiState()
  apiState.available = AVAILABLE
  apiState.grid = (arg) => isSimpleRequest(arg)
    ? { data: { items: [{ id: 5, columns: [{ key: 'name', locale: 'de', value: 'Sneaker' }] }] } }
    : { data: { items: [{ id: 5, columns: [{ key: 'fullpath', value: '/P/S' }] }] } }
  apiState.gridPreview = () => ({ data: { value: [{ type: 'string', value: 'Calculated' }] } })
})

describe('schema modal language switch with preview off', () => {
  test('AC1: with preview mode off the language switch is in the header row, set to the saved language, without the item picker', () => {
    render(modal('de'))

    const header = screen.getByTestId('preview-mode-header')
    expect(within(header).getByRole('switch')).toBeInTheDocument()
    expect((within(header).getByLabelText('preview-language') as HTMLSelectElement).value).toBe('de')
    expect(within(header).queryByText('preview-mode.preview-item')).not.toBeInTheDocument()
    expect(screen.queryByTestId('preview-mode-header-divider')).not.toBeInTheDocument()
  })

  test('AC1: changing the language with preview off saves it', () => {
    const onLanguageChange = jest.fn()
    render(modal('de', onLanguageChange))
    fireEvent.change(screen.getByLabelText('preview-language'), { target: { value: 'en' } })

    expect(onLanguageChange).toHaveBeenLastCalledWith('en')
  })

  test('AC2: switching preview mode on and off keeps the chosen language, the previews use it', () => {
    render(modal('de'))
    fireEvent.change(screen.getByLabelText('preview-language'), { target: { value: 'en' } })
    fireEvent.click(screen.getByRole('switch'))

    expect((screen.getByLabelText('preview-language') as HTMLSelectElement).value).toBe('en')
    const last = apiState.calls.grid.filter(isSimpleRequest).slice(-1)[0]
    expect(last.body.columns.find((column: any) => column.key === 'name').locale).toBe('en')

    fireEvent.click(screen.getByRole('switch'))
    expect((screen.getByLabelText('preview-language') as HTMLSelectElement).value).toBe('en')
  })

  test('AC3: the values sit in the shared preview row, with no border or background of their own', () => {
    render(modal('en'))
    fireEvent.click(screen.getByRole('switch'))

    const rows = screen.getAllByTestId('preview-mode-column-preview-row')
    expect(rows).toHaveLength(2)
    rows.forEach(row => {
      row.querySelectorAll<HTMLElement>('*').forEach(element => {
        expect(element.style.border).toBe('')
        expect(element.style.background).toBe('')
      })
    })
    expect(screen.getAllByTestId('preview-mode-column-preview-divider')).toHaveLength(2)
  })
})
