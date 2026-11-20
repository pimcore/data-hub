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
import { Input } from 'antd'
import { render, screen, within } from '@testing-library/react'
import { apiState, resetApiState } from './mocks/studio-ui-bundle/api-state'
import { ColumnConfigModal } from '../src/modules/config/components/column-config-modal/column-config-modal'
import { BaseColumnEditor } from '../src/modules/config/components/column-config-modal/base-column-editor'
import { ColumnRowActions } from '../src/modules/config/components/column-config-modal/column-editor-item'
import { columnMarks } from '../src/modules/config/components/column-config-modal/column-marks'
import { type SchemaColumn } from '../src/modules/config/components/column-config-modal/types'

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

jest.mock('../src/modules/config/components/column-config-modal/column-locale-control', () => ({
  ColumnLocaleControl: () => <select aria-label='locale-control' />
}))

// a control that shows whether the settings are handed the read-only state
jest.mock('../src/modules/config/components/column-config-modal/column-pipeline-form', () => ({
  ColumnPipelineForm: ({ disabled }: { disabled?: boolean }) => (
    <Input
      aria-label='pipeline-setting'
      disabled={ disabled }
    />
  )
}))

const COLUMNS: SchemaColumn[] = [
  { key: 'name', fieldtype: 'input', type: 'dataobject.input', config: {} },
  { key: 'Calc', fieldtype: 'advanced', type: 'dataobject.advanced', config: { advancedColumns: [], transformers: [] } }
]

interface ModalOptions {
  columns?: SchemaColumn[]
  columnConfig?: Record<string, unknown>
  previousColumns?: SchemaColumn[]
  onApply?: () => void
}

const modal = (
  onLanguageChange: (language: string) => void = () => {},
  readOnly?: boolean,
  { columns = COLUMNS, columnConfig, previousColumns, onApply = () => {} }: ModalOptions = {}
): React.JSX.Element => (
  <ColumnConfigModal
    columnConfig={ columnConfig }
    columns={ columns }
    entity='Product'
    onApply={ onApply }
    onCancel={ () => {} }
    onLanguageChange={ onLanguageChange }
    open
    previousColumns={ previousColumns }
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

describe('column configurator view only', () => {
  test('readOnly: no apply, add, remove or reorder, settings disabled, columns listed', () => {
    const onLanguageChange = jest.fn()
    render(modal(onLanguageChange, true))

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

  test('stays editable without readOnly', () => {
    render(modal())

    expect(screen.getByText('data-hub.column-config-modal.apply')).toBeInTheDocument()
    expect(screen.getByText('data-hub.column-config-modal.add-column')).toBeInTheDocument()
    // one trash per column
    expect(document.querySelectorAll('li button')).toHaveLength(2)
    expect(screen.getByLabelText('pipeline-setting')).not.toBeDisabled()
  })

  test('readOnly with a legacy configuration: the legacy columns are shown, no migration can start', () => {
    const onApply = jest.fn()
    render(modal(() => {}, true, { columns: [], columnConfig: { columns: [{ attribute: 'legacySku' }] }, onApply }))

    // the legacy configuration, not an empty editor
    expect(screen.getByText(/legacySku/)).toBeInTheDocument()
    expect(screen.queryByText('data-hub.migration-modal.start-migration')).toBeNull()
    expect(screen.queryByText('data-hub.migration-modal.confirm-migration')).toBeNull()
    expect(screen.queryByText('data-hub.column-config-modal.add-column')).toBeNull()
    expect(screen.queryByText('data-hub.column-config-modal.apply')).toBeNull()
    expect(onApply).not.toHaveBeenCalled()
  })

  test('a legacy configuration stays migratable without readOnly', () => {
    render(modal(() => {}, false, { columns: [], columnConfig: { columns: [{ attribute: 'legacySku' }] } }))

    expect(screen.getByText('data-hub.migration-modal.start-migration')).toBeInTheDocument()
  })

  test('readOnly with previous columns: added and changed columns marked, removed ones listed', () => {
    const previous = [
      { key: 'name', fieldtype: 'input', type: 'dataobject.input', config: {} },
      { key: 'Calc', fieldtype: 'advanced', type: 'dataobject.advanced', config: { advancedColumns: [], transformers: ['trim'] } },
      { key: 'ean', fieldtype: 'input', type: 'dataobject.input', config: {} }
    ]
    const columns = [
      COLUMNS[0],
      COLUMNS[1],
      { key: 'productionYear', fieldtype: 'numeric', type: 'dataobject.numeric', config: {} }
    ]
    render(modal(() => {}, true, { columns, previousColumns: previous }))

    const items = screen.getAllByRole('listitem')
    expect(items).toHaveLength(4)
    expect(within(items[0]).queryByTestId(/column-mark-/)).toBeNull()
    expect(within(items[1]).getByTestId('column-mark-changed').textContent).toBe('form.annotation.changed')
    expect(within(items[2]).getByText('productionYear')).toBeInTheDocument()
    expect(within(items[2]).getByTestId('column-mark-added')).toBeInTheDocument()
    expect(within(items[3]).getByText('ean')).toBeInTheDocument()
    expect(within(items[3]).getByTestId('column-mark-removed')).toBeInTheDocument()
  })

  test('previous columns mark nothing when the configurator is editable', () => {
    render(modal(() => {}, false, { previousColumns: [] }))

    expect(screen.queryByTestId(/column-mark-/)).toBeNull()
    expect(screen.getAllByRole('listitem')).toHaveLength(2)
  })

  test('readOnly: a localizable column shows its locale as text, no locale control', () => {
    const column = { _id: 'c1', key: 'title', fieldtype: 'advanced', type: 'dataobject.advanced', localizable: true, locale: 'de' }
    const onLocaleChange = jest.fn()
    const { rerender } = render(
      <ColumnRowActions
        column={ column }
        onLocaleChange={ onLocaleChange }
        onRemove={ () => {} }
        readOnly
      />
    )

    expect(screen.getByText('de')).toBeInTheDocument()
    expect(screen.queryByLabelText('locale-control')).toBeNull()
    expect(screen.queryByRole('button')).toBeNull()

    rerender(
      <ColumnRowActions
        column={ { ...column, locale: null } }
        onLocaleChange={ onLocaleChange }
        onRemove={ () => {} }
        readOnly
      />
    )
    expect(screen.getByText('-')).toBeInTheDocument()

    rerender(
      <ColumnRowActions
        column={ column }
        onLocaleChange={ onLocaleChange }
        onRemove={ () => {} }
      />
    )
    expect(screen.getByLabelText('locale-control')).toBeInTheDocument()
  })
})

describe('columnMarks', () => {
  const input = (key: string, locale?: string | null): SchemaColumn =>
    ({ key, fieldtype: 'input', type: 'dataobject.input', config: {}, locale })

  test('no previous columns: nothing marked', () => {
    expect(columnMarks([input('a')])).toEqual({ marks: [undefined], removed: [] })
  })

  test('a column is told apart by its locale', () => {
    expect(columnMarks([input('a', 'de')], [input('a', 'en')])).toEqual({ marks: ['added'], removed: [input('a', 'en')] })
  })

  test('an unset config or locale equals an empty one', () => {
    const { config, ...bare } = input('a')
    expect(columnMarks([bare], [input('a', null)]).marks).toEqual([undefined])
  })
})
