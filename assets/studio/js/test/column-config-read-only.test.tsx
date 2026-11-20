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
import { ConfigProvider, Input } from 'antd'
import { render, screen } from '@testing-library/react'
import { apiState, resetApiState } from './mocks/studio-ui-bundle/api-state'
import { ColumnConfigModal } from '../src/modules/config/components/column-config-modal/column-config-modal'
import { BaseColumnEditor } from '../src/modules/config/components/column-config-modal/base-column-editor'

// the shared stand-ins drop the toolbars and the sort flag, which are what a reader must not get
jest.mock('@pimcore/studio-ui-bundle/components', () => ({
  ...jest.requireActual('@pimcore/studio-ui-bundle/components'),
  ContentLayout: ({ renderTopBar, renderToolbar, children }: Record<string, React.ReactNode>) => (
    <div>{ renderTopBar }{ renderToolbar }{ children }</div>
  ),
  StackList: ({ items, sortable }: { items: any[], sortable?: boolean }) => (
    <ul data-sortable={ String(sortable) }>
      { items.map((item) => (
        <li
          data-sortable={ String(item.sortable) }
          data-testid={ `column-${item.key ?? item.id}` }
          key={ item.id }
        >{ item.children }{ item.body }{ item.renderRightToolbar }</li>
      )) }
    </ul>
  )
}))

jest.mock('../src/modules/config/components/column-config-modal/base-column-editor.styles', () => ({
  useStyles: () => ({ styles: {} })
}))

// a real AntD control, so the test sees whether the settings follow the read-only state
jest.mock('../src/modules/config/components/column-config-modal/column-pipeline-form', () => ({
  ColumnPipelineForm: () => <Input aria-label='pipeline-setting' />
}))

const COLUMNS = [
  { key: 'name', fieldtype: 'input', type: 'dataobject.input', config: {} },
  { key: 'Calc', fieldtype: 'advanced', type: 'dataobject.advanced', config: { advancedColumns: [], transformers: [] } }
]

const modal = (onLanguageChange: (language: string) => void = () => {}, readOnly?: boolean): React.JSX.Element => (
  <ColumnConfigModal
    columnConfig={ undefined }
    columns={ COLUMNS }
    entity='Product'
    onApply={ () => {} }
    onCancel={ () => {} }
    onLanguageChange={ onLanguageChange }
    open
    readOnly={ readOnly }
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
  apiState.available = {
    data: { columns: [{ key: 'name', type: 'dataobject.input' }, { key: 'advanced', type: 'dataobject.advanced', config: {} }] },
    isLoading: false
  }
})

describe('column configurator in a disabled form', () => {
  test('opens view only: no apply, add, remove or reorder, settings disabled, columns listed', () => {
    const onLanguageChange = jest.fn()
    render(<ConfigProvider componentDisabled>{ modal(onLanguageChange) }</ConfigProvider>)

    expect(screen.getByText('name')).toBeInTheDocument()
    expect(screen.getByText('Calc')).toBeInTheDocument()

    expect(screen.queryByText('data-hub.column-config-modal.apply')).toBeNull()
    expect(screen.queryByText('data-hub.column-config-modal.discard')).toBeNull()
    expect(screen.queryByText('data-hub.column-config-modal.add-column')).toBeNull()
    expect(screen.queryByText('icon')).toBeNull()

    const sortFlags = Array.from(document.querySelectorAll('[data-sortable]')).map((node) => node.getAttribute('data-sortable'))
    expect(sortFlags).not.toContain('true')

    expect(screen.getByLabelText('pipeline-setting')).toBeDisabled()
    // looking does not write the preview language back into the form
    expect(onLanguageChange).not.toHaveBeenCalled()
  })

  test('an explicit readOnly wins over an enabled form', () => {
    render(modal(() => {}, true))

    expect(screen.queryByText('data-hub.column-config-modal.apply')).toBeNull()
    expect(screen.queryByText('icon')).toBeNull()
  })

  test('stays editable in an enabled form', () => {
    render(modal())

    expect(screen.getByText('data-hub.column-config-modal.apply')).toBeInTheDocument()
    expect(screen.getByText('data-hub.column-config-modal.add-column')).toBeInTheDocument()
    // one trash per column
    expect(document.querySelectorAll('li button')).toHaveLength(2)
    expect(screen.getByLabelText('pipeline-setting')).not.toBeDisabled()
  })
})
