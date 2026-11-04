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
import { Modal } from './mocks/studio-ui-bundle/components'
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

const TYPES = ['dataobject.adapter', 'dataobject.objectbrick', 'dataobject.classificationstore', 'dataobject.other']

const COLUMNS = TYPES.map((type, i) => ({ key: `c${i}`, fieldtype: 'input', type }))

beforeEach(() => {
  resetApiState()
  apiState.available = { data: { columns: [] }, isLoading: false }
  apiState.grid = (arg) => arg.body.filters.columnFilters === undefined
    ? { data: { items: [{ id: 5, columns: [] }] } }
    : { data: { items: [{ id: 5, columns: COLUMNS.map(column => ({ key: column.key, value: 'v' })) }] } }
})

describe('sonar findings', () => {
  test('AC1: adapter column types get a data object type, other types do not', () => {
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

    expect(screen.getByText('dataobject.adapter:input="v"')).toBeInTheDocument()
    expect(screen.getByText('dataobject.objectbrick:input="v"')).toBeInTheDocument()
    expect(screen.getByText('dataobject.classificationstore:input="v"')).toBeInTheDocument()
    expect(screen.getByText('dataobject.other:="v"')).toBeInTheDocument()
  })

  test('AC2: the open mock Modal is a dialog element with the open attribute and its children', () => {
    render(<Modal open title='T'><p>body</p></Modal>)

    const dialog = screen.getByRole('dialog')
    expect(dialog.tagName).toBe('DIALOG')
    expect(dialog).toHaveAttribute('open')
    expect(dialog).toHaveTextContent('Tbody')
  })
})
