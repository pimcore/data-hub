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
import { flatMap } from 'lodash'
import { type GridColumnConfiguration } from '@pimcore/studio-ui-bundle/api/data-object'
import { act, fireEvent, render, renderHook, screen } from '@testing-library/react'
import { apiState, resetApiState } from './mocks/studio-ui-bundle/api-state'
import { applyClassificationStoreSelection, openClassificationStoreModal } from './mocks/studio-ui-bundle/modules-element'
import { type ColumnEditorHandle } from '../src/modules/config/components/column-config-modal/types'
import { BaseColumnEditor } from '../src/modules/config/components/column-config-modal/base-column-editor'
import { useAddColumnGroups } from '../src/modules/config/components/column-config-modal/use-add-column-groups'
import { CLASSIFICATION_STORE_TYPE } from '../src/modules/config/components/column-config-modal/column-identity'

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
  openClassificationStoreModal.mockClear()
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

    expect(openClassificationStoreModal).not.toHaveBeenCalled()
    expect(editor.current?.getColumns()).toEqual([
      expect.objectContaining({ key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, config: undefined }),
      expect.objectContaining({ key: 'name', type: 'dataobject.input' })
    ])
  })

  it('opens the modal on the key tab with the store of the field, and appends the picked key', () => {
    const editor = mountEditor(NO_COLUMNS)

    fireEvent.click(screen.getByTestId(PICK))
    expect(openClassificationStoreModal).toHaveBeenCalledWith(expect.objectContaining({
      storeId: 3, fieldName: 'technicalAttributes', allowedTabs: ['group-by-key']
    }))
    act(() => { applyClassificationStoreSelection([{ groupId: 7, id: 42, name: 'rim' }]) })

    expect(editor.current?.getColumns()).toEqual([
      expect.objectContaining({ key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, config: { groupId: 7, keyId: 42 } })
    ])
  })

  it('opens it multi-select and appends one column per key of the selection', () => {
    const editor = mountEditor(NO_COLUMNS)

    fireEvent.click(screen.getByTestId(PICK))
    expect(openClassificationStoreModal).toHaveBeenCalledWith(expect.objectContaining({ singleSelection: false }))
    act(() => {
      applyClassificationStoreSelection([
        { groupId: 7, id: 42, name: 'rim' },
        { groupId: 7, id: 43, name: 'tyre' }
      ])
    })

    expect(editor.current?.getColumns().map(column => column.config)).toEqual([
      { groupId: 7, keyId: 42 },
      { groupId: 7, keyId: 43 }
    ])
  })

  it('adds nothing when the selection is applied empty', () => {
    const editor = mountEditor(NO_COLUMNS)

    fireEvent.click(screen.getByTestId(PICK))
    act(() => { applyClassificationStoreSelection([]) })

    expect(editor.current?.getColumns()).toEqual([])
  })

  it('is not offered when the class is empty, while the whole-store entry still is', () => {
    mountEditor(NO_COLUMNS, '')

    expect(screen.queryByTestId(PICK)).not.toBeInTheDocument()
    expect(screen.getByTestId('picker-item-technicalAttributes')).toBeInTheDocument()
  })

  it('is not offered by the legacy migration popover, which hosts no key picker', () => {
    const { result } = renderHook(() => useAddColumnGroups([storeField]))
    const keys = flatMap(result.current, group => group.items.map(item => item.key))

    expect(keys).toEqual(['technicalAttributes'])
  })

  it('is offered by the grouped tree only when the caller says it can host the picker', () => {
    const { result } = renderHook(() => useAddColumnGroups([storeField], true))

    expect(flatMap(result.current, group => group.items.map(item => item.key))).toEqual(['technicalAttributes', 'technicalAttributes-pick-key'])
  })

  it('falls back to store 1 when the field never had a store configured', () => {
    const bare = { ...storeField, config: { fieldDefinition: { fieldtype: 'classificationstore' } } } as unknown as GridColumnConfiguration
    apiState.available = { data: { columns: [bare] }, isLoading: false }
    mountEditor(NO_COLUMNS)

    fireEvent.click(screen.getByTestId(PICK))

    expect(openClassificationStoreModal).toHaveBeenCalledWith(expect.objectContaining({ storeId: 1 }))
  })
})
