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

const CS = 'dataobject.classificationstore'
const field = (keyId: number, groupId: number): any => ({
  key: 'color', type: CS, localizable: false, group: ['g'], config: { keyId, groupId, fieldDefinition: { fieldtype: 'input' } }
})

const AVAILABLE = { data: { columns: [field(1, 10), field(1, 11), { key: 'name', type: 'dataobject.adapter', localizable: false, group: ['g'], config: {} }] }, isLoading: false }

const column = (keyId: number, groupId: number): any => ({ key: 'color', fieldtype: 'input', type: CS, config: { keyId, groupId } })
const COLUMNS = [column(1, 10), column(1, 11), { key: 'name', fieldtype: 'input', type: 'dataobject.adapter' }]

const isSimpleRequest = (arg: any): boolean => arg.body.filters.columnFilters !== undefined

const open = (): void => {
  render(
    <ColumnConfigModal
      columns={ COLUMNS }
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
  fireEvent.click(screen.getByRole('switch'))
}

beforeEach(() => {
  resetApiState()
  apiState.available = AVAILABLE
  apiState.gridPreview = () => ({})
})

const respond = (columns: any[]): void => {
  apiState.grid = (arg) => isSimpleRequest(arg)
    ? { data: { items: [{ id: 5, columns }] } }
    : { data: { items: [{ id: 5, columns: [] }] } }
}

describe('classification-store identity', () => {
  test('AC1: siblings with the same key but different key or group id each send their own config', () => {
    respond([])
    open()
    const request = apiState.calls.grid.filter(isSimpleRequest).pop()
    const colors = request.body.columns.filter((c: any) => c.key === 'color')
    expect(colors.map((c: any) => c.config.groupId)).toEqual([10, 11])
  })

  test('AC2: each sibling shows its own value, and one without a matching entry shows no data', () => {
    respond([
      { key: 'color', type: CS, value: 'red', additionalAttributes: { keyId: 1, groupId: 10 } },
      { key: 'name', value: 'N' }
    ])
    open()
    expect(screen.getByText(/"red"/)).toBeInTheDocument()
    expect(screen.getAllByText(/"red"/)).toHaveLength(1)
    expect(screen.getByText('data-hub.column-config-modal.preview.no-data')).toBeInTheDocument()
  })

  test('AC3: a column that is not classification-store matches by key and locale', () => {
    respond([{ key: 'name', value: 'N' }])
    open()
    expect(screen.getByText(/adapter:input="N"|dataobject.adapter:.*"N"/)).toBeInTheDocument()
  })
})
