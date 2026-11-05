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

const AVAILABLE = {
  data: {
    columns: [
      { key: 'name', type: 'dataobject.adapter', localizable: false, group: ['g'], config: { fieldDefinition: { fieldtype: 'input' } } }
    ]
  },
  isLoading: false
}

const SIMPLE = { key: 'name', fieldtype: 'input', type: 'dataobject.adapter' }
const rowFor = (id: number, value: string): any => ({ items: [{ id, columns: [{ key: 'name', value }] }] })

const isSimpleRequest = (arg: any): boolean => arg.body.filters.columnFilters !== undefined

const modal = (entity: string): React.JSX.Element => (
  <ColumnConfigModal
    columns={ [SIMPLE] }
    entity={ entity }
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

beforeEach(() => {
  resetApiState()
  apiState.available = AVAILABLE
  apiState.gridPreview = () => ({})
})

describe('preview reads currentData', () => {
  test('AC1: a changed item shows the spinner, not the previous value', () => {
    apiState.grid = (arg) => isSimpleRequest(arg)
      ? { data: rowFor(5, 'Old'), currentData: undefined, isFetching: true }
      : { data: rowFor(5, 'Old') }
    render(modal('Product'))
    fireEvent.click(screen.getByRole('switch'))

    expect(screen.getByText('spin')).toBeInTheDocument()
    expect(screen.queryByText(/Old/)).not.toBeInTheDocument()
  })

  test('AC2: a refetch with unchanged arguments keeps the value without a spinner', () => {
    apiState.grid = (arg) => isSimpleRequest(arg)
      ? { data: rowFor(5, 'Kept'), currentData: rowFor(5, 'Kept'), isFetching: true }
      : { data: rowFor(5, 'Kept') }
    render(modal('Product'))
    fireEvent.click(screen.getByRole('switch'))

    expect(screen.getByText(/Kept/)).toBeInTheDocument()
    expect(screen.queryByText('spin')).not.toBeInTheDocument()
  })

  test('AC3: after a class change the previous class row is not preselected; the new one is once current', () => {
    let current = true
    apiState.grid = (arg) => {
      if (isSimpleRequest(arg)) return { data: rowFor(arg.body.filters.columnFilters[0].filterValue[0] as number, 'V'), isFetching: false }
      if (arg.classId === 'class-Product') return { data: rowFor(5, 'V') }
      return current ? { data: rowFor(7, 'V') } : { data: rowFor(5, 'V'), currentData: undefined, isFetching: true }
    }
    const { rerender } = render(modal('Product'))
    fireEvent.click(screen.getByRole('switch'))
    expect(apiState.calls.grid.some((c) => isSimpleRequest(c) && c.body.filters.columnFilters[0].filterValue[0] === 5)).toBe(true)

    apiState.calls.grid = []
    current = false
    rerender(modal('Other'))
    expect(apiState.calls.grid.filter(isSimpleRequest).filter((c) => c.classId === 'class-Other')).toHaveLength(0)

    current = true
    rerender(modal('Other'))
    const ids = apiState.calls.grid.filter(isSimpleRequest).filter((c) => c.classId === 'class-Other').map((c) => c.body.filters.columnFilters[0].filterValue[0])
    expect(ids).toContain(7)
    expect(ids).not.toContain(5)
  })
})
