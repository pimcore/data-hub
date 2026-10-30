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

// Collapse-aware StackList: the body only while open (defaultActive), collapsedBody only while closed
jest.mock('@pimcore/studio-ui-bundle/components', () => {
  const actual = jest.requireActual('./mocks/studio-ui-bundle/components')
  return {
    ...actual,
    StackList: ({ items }: any) => (
      <ul>
        { items.map((item: any) => (
          <li
            data-testid={ `column-${item.id}` }
            key={ item.id }
          >
            { item.children }
            { item.defaultActive === true ? item.body : undefined }
            { item.defaultActive !== true ? item.collapsedBody : undefined }
          </li>
        )) }
      </ul>
    )
  }
})

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

const modal = (columns: any[]): React.JSX.Element => (
  <ColumnConfigModal
    columns={ columns }
    entity='Product'
    language='en'
    onApply={ () => {} }
    onCancel={ () => {} }
    onLanguageChange={ () => {} }
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

const ADVANCED = { key: 'Calc', fieldtype: 'advanced', type: 'dataobject.advanced', config: { advancedColumns: [], transformers: [] } }
const available = (config: any): any => ({
  data: { columns: [{ key: 'advanced', type: 'dataobject.advanced', localizable: true, config }] },
  isLoading: false
})

beforeEach(() => {
  resetApiState()
  apiState.available = available({})
  apiState.grid = () => ({ data: { items: [{ id: 5, columns: [] }] } })
  apiState.gridPreview = () => ({ data: { value: [{ type: 'string', value: 'Calculated' }] } })
})

describe('preview row on collapsed advanced cards', () => {
  test('AC1: a collapsed advanced card shows one Preview row with the value', () => {
    render(modal([ADVANCED]))
    fireEvent.click(screen.getByRole('switch'))

    expect(screen.getAllByTestId('preview-mode-column-preview-row')).toHaveLength(1)
    expect(screen.getByTestId('preview-grid')).toHaveTextContent('Calculated')
  })

  test('AC1: an open (new) advanced card shows exactly one row, in its form', () => {
    render(modal([{ ...ADVANCED, isNew: true }]))
    fireEvent.click(screen.getByRole('switch'))

    expect(screen.getAllByTestId('preview-mode-column-preview-row')).toHaveLength(1)
  })

  test('AC2: while pipelineConfig is loading the collapsed card shows the loading state and sends no request', () => {
    apiState.available = available(undefined)
    render(modal([{ key: 'Calc', fieldtype: 'advanced', type: 'dataobject.advanced' }]))
    fireEvent.click(screen.getByRole('switch'))

    expect(screen.getAllByTestId('preview-mode-column-preview-row')).toHaveLength(1)
    expect(screen.getByText('data-hub.column-config-modal.preview.loading')).toBeInTheDocument()
    expect(apiState.calls.gridPreview).toHaveLength(0)
  })

  test('AC2: with an undefined pipeline the collapsed card shows no value and sends no request', () => {
    render(modal([{ key: 'Calc', fieldtype: 'advanced', type: 'dataobject.advanced' }]))
    fireEvent.click(screen.getByRole('switch'))

    expect(screen.getByText('data-hub.column-config-modal.preview.no-data')).toBeInTheDocument()
    expect(screen.queryByTestId('preview-grid')).not.toBeInTheDocument()
    expect(apiState.calls.gridPreview).toHaveLength(0)
  })

  test('AC3: with preview mode off a collapsed card shows no Preview row', () => {
    render(modal([ADVANCED]))

    expect(screen.queryByTestId('preview-mode-column-preview-row')).not.toBeInTheDocument()
  })
})
