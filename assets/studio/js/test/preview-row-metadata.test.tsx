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

const mockRows: any[][] = []

jest.mock('@pimcore/studio-ui-bundle/components', () => ({
  ...jest.requireActual('@pimcore/studio-ui-bundle/components'),
  Grid: ({ data }: { data: any[] }) => {
    mockRows.push(data)
    return <div>{ String(data[0]?.value) }</div>
  }
}))

jest.mock('../src/modules/config/components/column-config-modal/base-column-editor.styles', () => ({
  useStyles: () => ({ styles: {} })
}))

const AVAILABLE = {
  data: {
    columns: [
      { key: 'name', type: 'dataobject.adapter', localizable: false, group: ['g'], config: { fieldDefinition: { fieldtype: 'input' } } }
    ]
  },
  isLoading: false
}

const SIMPLE = { key: 'name', fieldtype: 'input', type: 'dataobject.adapter' }
const isSimpleRequest = (arg: any): boolean => arg.body.filters.columnFilters !== undefined

const modal = (): React.JSX.Element => (
  <ColumnConfigModal
    columns={ [SIMPLE] }
    entity='Product'
    onApply={ () => {} }
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

const setup = (apiRow: any): void => {
  apiState.grid = (arg) => isSimpleRequest(arg)
    ? { data: { items: [apiRow] }, currentData: { items: [apiRow] }, isFetching: false }
    : { data: { items: [{ id: 5, columns: [] }] } }
  render(modal())
  fireEvent.click(screen.getByRole('switch'))
}

beforeEach(() => {
  resetApiState()
  mockRows.length = 0
  apiState.available = AVAILABLE
  apiState.gridPreview = () => ({})
})

describe('preview row metadata', () => {
  test('AC1: the grid row carries the API row id and the API row under __api-data', () => {
    const apiRow = { id: 5, isLocked: true, permissions: { view: true }, columns: [{ key: 'name', value: 'Val' }] }
    setup(apiRow)

    expect(screen.getByText('Val')).toBeInTheDocument()
    const row = mockRows[mockRows.length - 1][0]
    expect(row.id).toBe(5)
    expect(row.isLocked).toBe(true)
    expect(row.permissions).toEqual({ view: true })
    expect(row['__api-data']).toBe(apiRow)
    expect(row.value).toBe('Val')
  })

  test('AC2: an API row without id still renders the value', () => {
    setup({ columns: [{ key: 'name', value: 'NoId' }] })

    expect(screen.getByText('NoId')).toBeInTheDocument()
    const row = mockRows[mockRows.length - 1][0]
    expect(row.id).toBeUndefined()
    expect(row.value).toBe('NoId')
  })
})
