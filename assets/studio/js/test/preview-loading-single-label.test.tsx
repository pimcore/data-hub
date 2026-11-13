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

const SIMPLE = { key: 'name', fieldtype: 'input', type: 'dataobject.adapter' }
const ADVANCED = { key: 'Calc', fieldtype: 'advanced', type: 'dataobject.advanced', config: { advancedColumns: [], transformers: [] } }

const FIRST_OBJECT = { items: [{ id: 5, columns: [] }] }

const isSimpleRequest = (arg: any): boolean => arg.body.filters.columnFilters !== undefined

const modal = (columns: any[]): React.JSX.Element => (
  <ColumnConfigModal
    columns={ columns }
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

beforeEach(() => {
  resetApiState()
  apiState.available = AVAILABLE
  // the simple request stays pending, the preselect request answers
  apiState.grid = (arg) => isSimpleRequest(arg) ? { isFetching: true } : { data: FIRST_OBJECT }
  apiState.gridPreview = () => ({ isFetching: true })
})

describe('preview loading shows the label once', () => {
  test('AC1: a simple column with pending values has one row, one label, a spinner and no nested row', () => {
    render(modal([SIMPLE]))
    fireEvent.click(screen.getByRole('switch'))

    const rows = screen.getAllByTestId('preview-mode-column-preview-row')
    expect(rows).toHaveLength(1)
    expect(screen.getAllByTestId('preview-mode-column-preview-divider')).toHaveLength(1)
    expect(screen.getAllByText('preview-mode.preview')).toHaveLength(1)
    expect(within(rows[0]).getByText('spin')).toBeInTheDocument()
    expect(within(rows[0]).queryByTestId('preview-mode-column-preview-row')).not.toBeInTheDocument()
  })

  test('AC2: an advanced card with a pending request has one row with one label, open and collapsed', () => {
    render(modal([ADVANCED]))
    fireEvent.click(screen.getByRole('switch'))
    expect(screen.getAllByTestId('preview-mode-column-preview-row')).toHaveLength(1)
    expect(screen.getAllByText('preview-mode.preview')).toHaveLength(1)
  })

  test('AC2: an open (new) advanced card with a pending request has one row with one label', () => {
    render(modal([{ ...ADVANCED, isNew: true }]))
    fireEvent.click(screen.getByRole('switch'))
    expect(screen.getAllByTestId('preview-mode-column-preview-row')).toHaveLength(1)
    expect(screen.getAllByText('preview-mode.preview')).toHaveLength(1)
  })
})
