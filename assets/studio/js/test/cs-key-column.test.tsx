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
import { type GridColumnConfiguration } from '@pimcore/studio-ui-bundle/api/data-object'
import { act, fireEvent, render, renderHook, screen } from '@testing-library/react'
import { apiState, resetApiState } from './mocks/studio-ui-bundle/api-state'
import { trackedErrors } from './mocks/studio-ui-bundle/modules-app'
import { openClassificationStorePicker } from './mocks/studio-ui-bundle/modules-element'
import { type ColumnEditorHandle } from '../src/modules/config/components/column-config-modal/types'
import { BaseColumnEditor } from '../src/modules/config/components/column-config-modal/base-column-editor'
import { useAddColumnGroups } from '../src/modules/config/components/column-config-modal/use-add-column-groups'
import { useColumnEditorState } from '../src/modules/config/components/column-config-modal/use-column-editor-state'
import {
  CLASSIFICATION_STORE_TYPE,
  classificationStoreId,
  columnLabel
} from '../src/modules/config/components/column-config-modal/column-identity'

jest.mock('../src/modules/config/components/column-config-modal/base-column-editor.styles', () => ({
  useStyles: () => ({ styles: {} })
}))

const storeField = {
  key: 'technicalAttributes',
  type: CLASSIFICATION_STORE_TYPE,
  localizable: false,
  group: ['Technical'],
  exportable: true,
  config: { fieldDefinition: { fieldtype: 'classificationstore', storeId: 3, title: 'Technical Attributes' } }
} as unknown as GridColumnConfiguration

const AVAILABLE = { data: { columns: [storeField] }, isLoading: false }

const NO_COLUMNS: any[] = []

// The editor's draft is read through its handle: what Apply submits, and whether Apply may submit it
const mountEditor = (columns: any[], classDefinitionId?: string): React.RefObject<ColumnEditorHandle> => {
  const ref = React.createRef<ColumnEditorHandle>()
  render(
    <BaseColumnEditor
      classDefinitionId={ classDefinitionId }
      columns={ columns }
      entity='Product'
      onApply={ () => {} }
      onCancel={ () => {} }
      ref={ ref }
      sourceFieldsRegistryId='source'
      transformersRegistryId='transformers'
    />
  )
  return ref
}

beforeEach(() => {
  resetApiState()
  apiState.available = AVAILABLE
  trackedErrors.length = 0
  openClassificationStorePicker.mockClear()
})

describe('adding a Classification Store field from the tree', () => {
  it('persists the bare field definition type, not the field name', () => {
    const editor = mountEditor(NO_COLUMNS)

    fireEvent.click(screen.getByTestId('picker-item-technicalAttributes'))

    expect(editor.current?.isHydrated?.()).toBe(true)
    expect(editor.current?.getColumns()).toEqual([
      expect.objectContaining({ key: 'technicalAttributes', fieldtype: 'classificationstore', type: CLASSIFICATION_STORE_TYPE })
    ])
  })

  it('writes the bare type back for a configuration stored with the field name', () => {
    const editor = mountEditor([{ key: 'technicalAttributes', fieldtype: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE }])

    expect(editor.current?.getColumns()[0].fieldtype).toBe('classificationstore')
  })
})

describe('adding one key of a Classification Store field', () => {
  it('appends a column carrying groupId and keyId and leaves the whole-store path alone', () => {
    const { result } = renderHook(() => useColumnEditorState({
      entity: 'Product', columns: NO_COLUMNS, onApply: () => {}, onCancel: () => {}
    }))

    act(() => { result.current.handleAddColumnOfType(storeField) })
    act(() => { result.current.handleAddClassificationStoreKey(storeField, { groupId: 10, keyId: 4 }) })
    act(() => { result.current.handleAddClassificationStoreKey(storeField, { groupId: 11, keyId: 4 }) })

    const columns = result.current.getColumns()
    expect(columns).toHaveLength(3)
    expect(columns[0].config).toBeUndefined()
    expect(columns[1]).toEqual({
      key: 'technicalAttributes',
      fieldtype: '',
      type: CLASSIFICATION_STORE_TYPE,
      config: { groupId: 10, keyId: 4 },
      locale: undefined
    })
    expect(columns[2].config).toEqual({ groupId: 11, keyId: 4 })
  })
})

describe('the type of a single key', () => {
  const keyEntry = (groupId: number, keyId: number, fieldtype: string): GridColumnConfiguration => ({
    ...storeField,
    config: { groupId, keyId, fieldDefinition: { fieldtype } }
  }) as unknown as GridColumnConfiguration

  it('is the key\'s own type, not that of the store, when the payload carries the key', () => {
    apiState.available = { data: { columns: [storeField, keyEntry(10, 4, 'input')] }, isLoading: false }
    const { result } = renderHook(() => useColumnEditorState({
      entity: 'Product', columns: NO_COLUMNS, onApply: () => {}, onCancel: () => {}
    }))

    act(() => { result.current.handleAddClassificationStoreKey(storeField, { groupId: 10, keyId: 4 }) })

    expect(result.current.getColumns()[0].fieldtype).toBe('input')
  })

  it('survives a reload unchanged next to a sibling key of the same field name', () => {
    apiState.available = { data: { columns: [keyEntry(10, 1, 'input'), keyEntry(10, 2, 'classificationstore')] }, isLoading: false }
    const editor = mountEditor([
      { key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, config: { groupId: 10, keyId: 2 }, fieldtype: 'classificationstore' }
    ])

    expect(editor.current?.getColumns()[0].fieldtype).toBe('classificationstore')
  })

  it('does not label a whole-store column with the ids of a key it was hydrated from', () => {
    apiState.available = { data: { columns: [keyEntry(10, 1, 'input')] }, isLoading: false }
    mountEditor([{ key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, fieldtype: 'classificationstore' }])

    expect(screen.getAllByText('technicalAttributes').some(node => node.className.includes('ant-tag'))).toBe(true)
    expect(screen.queryByText('technicalAttributes-10-1')).not.toBeInTheDocument()
  })
})

describe('row label', () => {
  it('tells the whole store and each key of it apart, reading the ids from config', () => {
    expect(columnLabel({ key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE })).toBe('technicalAttributes')
    expect(columnLabel({ key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, config: { groupId: 10, keyId: 4 } }))
      .toBe('technicalAttributes-10-4')
    expect(columnLabel({ key: 'name', type: 'dataobject.input', config: { groupId: 10, keyId: 4 } })).toBe('name')
    expect(columnLabel({ key: '', type: CLASSIFICATION_STORE_TYPE })).toBe('')
  })

  it('is rendered per row', () => {
    mountEditor([
      { key: 'technicalAttributes', fieldtype: 'classificationstore', type: CLASSIFICATION_STORE_TYPE },
      { key: 'technicalAttributes', fieldtype: 'classificationstore', type: CLASSIFICATION_STORE_TYPE, config: { groupId: 10, keyId: 4 } }
    ])

    expect(screen.getByText('technicalAttributes-10-4')).toBeInTheDocument()
    expect(screen.getAllByText('technicalAttributes')).toHaveLength(1) // the whole-store row only; the tree entry carries the field title
  })
})

describe('store id', () => {
  it.each([
    ['configured', { fieldDefinition: { storeId: 3 } }, 3],
    ['absent', { fieldDefinition: {} }, 1],
    ['null', { fieldDefinition: { storeId: null } }, 1],
    ['a string', { fieldDefinition: { storeId: '3' } }, 1],
    ['no field definition', {}, 1],
    ['no config', undefined, 1]
  ])('%s', (_name, config, expected) => {
    expect(classificationStoreId(config)).toBe(expected)
  })
})

describe('a failed available-columns request', () => {
  it('shows an error instead of an empty tree, tracks it and blocks Apply', () => {
    apiState.available = { data: undefined, isLoading: false, isError: true, error: { status: 500 } }
    const editor = mountEditor(NO_COLUMNS)

    expect(screen.getByRole('alert')).toHaveTextContent('data-hub.column-config-modal.fields-to-add.error')
    expect(screen.queryByTestId('column-picker')).not.toBeInTheDocument()
    expect(trackedErrors.length).toBeGreaterThan(0)
    expect(editor.current?.isHydrated?.()).toBe(false)
  })
})

describe('the key picker affordance in the tree', () => {
  const adapterField = { key: 'name', type: 'dataobject.input', localizable: false, group: ['Technical'], exportable: true } as unknown as GridColumnConfiguration
  const PICK = 'picker-item-technicalAttributes-pick-key'

  it('is offered on the classification store entry only', () => {
    apiState.available = { data: { columns: [storeField, adapterField] }, isLoading: false }
    mountEditor(NO_COLUMNS)

    expect(screen.getAllByTestId(/-pick-key$/)).toHaveLength(1)
    expect(screen.getByTestId(PICK)).toBeInTheDocument()
  })

  it('leaves the plain entry and other entries adding exactly what they add today', () => {
    apiState.available = { data: { columns: [storeField, adapterField] }, isLoading: false }
    const editor = mountEditor(NO_COLUMNS)

    fireEvent.click(screen.getByTestId('picker-item-technicalAttributes'))
    fireEvent.click(screen.getByTestId('picker-item-name'))

    expect(openClassificationStorePicker).not.toHaveBeenCalled()
    expect(editor.current?.getColumns()).toEqual([
      expect.objectContaining({ key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, config: undefined }),
      expect.objectContaining({ key: 'name', type: 'dataobject.input' })
    ])
  })

  it('opens the picker with the store of the field and appends the picked key as one column', () => {
    const editor = mountEditor(NO_COLUMNS)

    fireEvent.click(screen.getByTestId(PICK))
    expect(openClassificationStorePicker).toHaveBeenCalledWith(expect.objectContaining({ storeId: 3, fieldName: 'technicalAttributes' }))
    act(() => { openClassificationStorePicker.mock.calls[0][0].onPick({ groupId: 7, keyId: 42, keyName: 'rim' }) })

    expect(editor.current?.getColumns()).toEqual([
      expect.objectContaining({ key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, config: { groupId: 7, keyId: 42 } })
    ])
  })

  it('is not offered when the class is empty, while the whole-store entry still is', () => {
    mountEditor(NO_COLUMNS, '')

    expect(screen.queryByTestId(PICK)).not.toBeInTheDocument()
    expect(screen.getByTestId('picker-item-technicalAttributes')).toBeInTheDocument()
  })

  it('is not offered by the legacy migration popover, which hosts no key picker', () => {
    const { result } = renderHook(() => useAddColumnGroups([storeField]))
    const keys = result.current.flatMap(group => group.items.map(item => item.key))

    expect(keys).toEqual(['technicalAttributes'])
  })

  it('is offered by the grouped tree only when the caller says it can host the picker', () => {
    const { result } = renderHook(() => useAddColumnGroups([storeField], true))

    expect(result.current.flatMap(group => group.items.map(item => item.key))).toEqual(['technicalAttributes', 'technicalAttributes-pick-key'])
  })

  it('falls back to store 1 when the field never had a store configured', () => {
    const bare = { ...storeField, config: { fieldDefinition: { fieldtype: 'classificationstore' } } } as unknown as GridColumnConfiguration
    apiState.available = { data: { columns: [bare] }, isLoading: false }
    mountEditor(NO_COLUMNS)

    fireEvent.click(screen.getByTestId(PICK))

    expect(openClassificationStorePicker).toHaveBeenCalledWith(expect.objectContaining({ storeId: 1 }))
  })
})

describe('a per-key column across save and reload', () => {
  it.each([
    ['integer ids', { groupId: 7, keyId: 42 }],
    ['identifier strings', { groupId: 'GRP_A', keyId: 'EF000010' }]
  ])('is handed back unchanged with %s', (_name, config) => {
    const editor = mountEditor([{ key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, fieldtype: 'classificationstore', config }])

    expect(editor.current?.getColumns()).toEqual([expect.objectContaining({ key: 'technicalAttributes', config })])
  })

  it('reads as three different rows next to the whole-store column', () => {
    mountEditor([
      { key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, fieldtype: 'classificationstore' },
      { key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, fieldtype: 'classificationstore', config: { groupId: 7, keyId: 42 } },
      { key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, fieldtype: 'classificationstore', config: { groupId: 7, keyId: 43 } }
    ])

    expect(screen.getByText('technicalAttributes-7-42')).toBeInTheDocument()
    expect(screen.getByText('technicalAttributes-7-43')).toBeInTheDocument()
  })
})
