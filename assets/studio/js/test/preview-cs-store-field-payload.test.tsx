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
import { settleKeyLabels } from './settle-key-labels'
import { ColumnConfigModal } from '../src/modules/config/components/column-config-modal/column-config-modal'
import { BaseColumnEditor } from '../src/modules/config/components/column-config-modal/base-column-editor'

jest.mock('../src/modules/config/components/column-config-modal/base-column-editor.styles', () => ({
  useStyles: () => ({ styles: {} })
}))

const CS = 'dataobject.classificationstore'
const FIELD = 'technicalAttributes'

// The shape data-object/grid/available-columns really answers with: one entry per store FIELD, carrying the
// field definition of the whole store and no group or key id. There is no entry per key.
const STORE_FIELD = {
  key: FIELD,
  type: CS,
  localizable: false,
  group: ['Structured Data'],
  config: { fieldDefinition: { fieldtype: 'classificationstore', name: FIELD, storeId: 1 } }
}

const AVAILABLE = { data: { columns: [STORE_FIELD] }, isLoading: false }

// A column the key picker added: the ids are its own, nothing else holds them.
const COLUMNS = [{ key: FIELD, fieldtype: 'classificationstore', type: CS, config: { groupId: 1, keyId: 3 } }] as any

const isSimpleRequest = (arg: any): boolean => arg.body.filters.columnFilters !== undefined

const open = (): void => {
  render(
    <ColumnConfigModal
      columns={ COLUMNS }
      entity='AccessoryPart'
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

/**
 * The answer the grid endpoint really gives: a store column is resolved only when its config names a group
 * and a key (ClassificationStoreResolver::hasKeyConfig), otherwise the entry comes back bare and empty.
 */
const respondLikeBackend = (arg: any): any => ({
  data: {
    items: [{
      id: 372,
      columns: arg.body.columns.map((column: any) => {
        const ids = column.config
        if (column.type !== CS) {
          return { key: column.key, locale: column.locale ?? null, fieldType: 'input', value: 'x', additionalAttributes: [] }
        }
        if (ids?.groupId === undefined || ids?.keyId === undefined) {
          return { key: column.key, locale: null, fieldType: CS, value: null, inheritance: null, additionalAttributes: [] }
        }
        return {
          key: `${column.key}.length`,
          locale: null,
          fieldType: 'quantityValue',
          value: { value: '327', unitId: 'mm' },
          additionalAttributes: { groupId: ids.groupId, keyId: ids.keyId }
        }
      })
    }]
  }
})

beforeEach(() => {
  resetApiState()
  apiState.available = AVAILABLE
  apiState.gridPreview = () => ({})
  apiState.grid = (arg) => isSimpleRequest(arg)
    ? respondLikeBackend(arg)
    : { data: { items: [{ id: 372, columns: [] }] } }
})

describe('classification-store preview against the real available-columns payload', () => {
  test('the request carries the ids of the column, which only the column holds', async () => {
    open()
    await settleKeyLabels()
    const request = apiState.calls.grid.filter(isSimpleRequest).pop()
    const column = request.body.columns.find((c: any) => c.key === FIELD)

    expect(column.config).toEqual(expect.objectContaining({ groupId: 1, keyId: 3 }))
  })

  test('the value of the key is shown, not "no data"', async () => {
    open()
    await settleKeyLabels()

    expect(screen.queryByText('data-hub.column-config-modal.preview.no-data')).not.toBeInTheDocument()
    expect(screen.getByText(/"327"/)).toBeInTheDocument()
  })

  test('the cell is rendered as the type of the key, not as the whole store', async () => {
    open()
    await settleKeyLabels()

    expect(screen.getByText(/:quantityValue=/)).toBeInTheDocument()
  })
})
