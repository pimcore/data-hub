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
import { act, render, renderHook, screen, waitFor } from '@testing-library/react'
import { apiState, resetApiState } from './mocks/studio-ui-bundle/api-state'
import { settleKeyLabels } from './settle-key-labels'
import { type ColumnEditorHandle } from '../src/modules/config/components/column-config-modal/types'
import { BaseColumnEditor } from '../src/modules/config/components/column-config-modal/base-column-editor'
import { useColumnEditorState } from '../src/modules/config/components/column-config-modal/use-column-editor-state'
import { CLASSIFICATION_STORE_TYPE, columnLabel } from '../src/modules/config/components/column-config-modal/column-identity'

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
})

describe('row label', () => {
  // the mocked translator answers the key itself, so a translated label reads as its own key
  const t = (key: string): string => key
  const storeKeys = new Map([
    ['1-3', { keyName: 'height', groupName: 'Dimensions', keyTitle: 'Height' }],
    ['3-3', { keyName: 'length', groupName: 'Rims', keyTitle: 'Length' }],
    ['1-9', { keyName: 'materialAddon', groupName: 'Dimensions', keyTitle: '' }]
  ])

  it('tells the whole store and each key of it apart, reading the ids from config', () => {
    expect(columnLabel({ key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE }, t)).toBe('technicalAttributes')
    expect(columnLabel({ key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, config: { groupId: 10, keyId: 4 } }, t))
      .toBe('technicalAttributes-10-4')
    expect(columnLabel({ key: 'name', type: 'dataobject.input', config: { groupId: 10, keyId: 4 } }, t)).toBe('name')
    expect(columnLabel({ key: '', type: CLASSIFICATION_STORE_TYPE }, t)).toBe('')
  })

  it('shows the field title of the matching available entry, as the add-column tree does', () => {
    expect(columnLabel({ key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE }, t, { availableFields: [storeField] }))
      .toBe('Technical Attributes')
    expect(columnLabel({ key: 'key', type: 'system.key' }, t, { availableFields: [{ key: 'key' }] }))
      .toBe('key')
  })

  it('shows a configured key as its title and the group it was picked under', () => {
    expect(columnLabel(
      { key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, config: { groupId: 1, keyId: 3 } },
      t,
      { storeKeys }
    )).toBe('Height [Dimensions]')
  })

  it('names the group the key was configured under when it belongs to several', () => {
    expect(columnLabel(
      { key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, config: { groupId: 3, keyId: 3 } },
      t,
      { storeKeys }
    )).toBe('Length [Rims]')
  })

  it('falls back to the key name when the title is empty', () => {
    expect(columnLabel(
      { key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, config: { groupId: 1, keyId: 9 } },
      t,
      { storeKeys }
    )).toBe('materialAddon [Dimensions]')
  })

  it('shows the title the picker reported while the lookup has not answered', () => {
    expect(columnLabel(
      { key: 'technicalAttributes', type: CLASSIFICATION_STORE_TYPE, config: { groupId: 1, keyId: 3 }, keyTitle: 'Height' },
      t
    )).toBe('Height')
  })

  it('is rendered per row', async () => {
    mountEditor([
      { key: 'technicalAttributes', fieldtype: 'classificationstore', type: CLASSIFICATION_STORE_TYPE },
      { key: 'technicalAttributes', fieldtype: 'classificationstore', type: CLASSIFICATION_STORE_TYPE, config: { groupId: 10, keyId: 4 } }
    ])
    await settleKeyLabels()

    expect(screen.getByText('technicalAttributes-10-4')).toBeInTheDocument()
    // the tree entry and the whole-store row both carry the field title now
    expect(screen.getAllByText('Technical Attributes')).toHaveLength(2)
  })
})

describe('labelling a stored key, the path that needs the lookup', () => {
  const relations = {
    data: {
      totalItems: 2,
      items: [
        { keyId: 3, groupId: 1, keyName: 'height', groupName: 'Dimensions', keyTitle: 'Height' },
        { keyId: 3, groupId: 3, keyName: 'length', groupName: 'Rims', keyTitle: 'Length' }
      ]
    }
  }

  it('reads the key labels once for the store field and renders them', async () => {
    apiState.keyGroupRelations = () => relations
    mountEditor([
      { key: 'technicalAttributes', fieldtype: 'quantityValue', type: CLASSIFICATION_STORE_TYPE, config: { groupId: 1, keyId: 3 } },
      { key: 'technicalAttributes', fieldtype: 'quantityValue', type: CLASSIFICATION_STORE_TYPE, config: { groupId: 3, keyId: 3 } }
    ])

    expect(await screen.findByText('Height [Dimensions]')).toBeInTheDocument()
    expect(screen.getByText('Length [Rims]')).toBeInTheDocument()
    expect(apiState.calls.keyGroupRelations).toHaveLength(1)
    expect(apiState.calls.keyGroupRelations[0]).toEqual(
      expect.objectContaining({ storeId: 3, fieldName: 'technicalAttributes', page: 1 })
    )
  })

  it('is not asked at all for a configuration naming no key of a store', () => {
    apiState.keyGroupRelations = () => relations
    mountEditor([{ key: 'technicalAttributes', fieldtype: 'classificationstore', type: CLASSIFICATION_STORE_TYPE }])

    expect(apiState.calls.keyGroupRelations).toHaveLength(0)
  })

  it('keeps the identifier when the lookup knows nothing of the key', async () => {
    apiState.keyGroupRelations = () => ({ data: { totalItems: 0, items: [] } })
    mountEditor([
      { key: 'technicalAttributes', fieldtype: 'quantityValue', type: CLASSIFICATION_STORE_TYPE, config: { groupId: 1, keyId: 3 } }
    ])

    await waitFor(() => { expect(apiState.calls.keyGroupRelations).toHaveLength(1) })
    expect(screen.getByText('technicalAttributes-1-3')).toBeInTheDocument()
  })
})

describe('the title a freshly picked key carries', () => {
  it('reaches the draft column without being persisted', async () => {
    const { result } = renderHook(() => useColumnEditorState({
      entity: 'Product', columns: NO_COLUMNS, onApply: () => {}, onCancel: () => {}
    }))

    act(() => {
      result.current.handleAddClassificationStoreKey(storeField, [{ groupId: 1, keyId: 3, keyTitle: 'Height' }])
    })
    await settleKeyLabels()

    expect(result.current.draft[0].keyTitle).toBe('Height')
    expect(result.current.getColumns()[0]).not.toHaveProperty('keyTitle')
  })
})

