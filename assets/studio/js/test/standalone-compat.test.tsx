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
import { render, screen } from '@testing-library/react'
import { apiState, resetApiState } from './mocks/studio-ui-bundle/api-state'
import { ColumnPreview } from '../src/modules/config/components/column-config-modal/column-preview'
import { ColumnPipelineForm, type ColumnPipelineFormProps } from '../src/modules/config/components/column-config-modal/column-pipeline-form'
import { ColumnEditorItemBody, type ColumnEditorItemBodyProps } from '../src/modules/config/components/column-config-modal/column-editor-item'
import { BaseColumnEditor } from '../src/modules/config/components/column-config-modal/base-column-editor'

jest.mock('../src/modules/config/components/column-config-modal/base-column-editor.styles', () => ({
  useStyles: () => ({ styles: {} })
}))

const COLUMN: any = { _id: 'x', key: 'Calc', fieldtype: 'advanced', type: 'dataobject.advanced', pipeline: { sourceFields: [], transformers: [] } }

beforeEach(() => {
  resetApiState()
  apiState.available = { data: { columns: [] }, isLoading: false }
  apiState.gridPreview = () => ({ data: { value: [{ type: 'string', value: 'Calculated' }] } })
})

describe('standalone use without a PreviewModeProvider', () => {
  test('AC1: ColumnPreview with objectId 5 requests and shows the preview for object 5', () => {
    render(
      <ColumnPreview
        column={ COLUMN }
        objectId={ 5 }
      />
    )

    expect(apiState.calls.gridPreview[0].body.objectId).toBe(5)
    expect(screen.getByTestId('preview-grid')).toHaveTextContent('Calculated')
  })

  test('AC2: ColumnPreview with objectId null shows the placeholder', () => {
    render(
      <ColumnPreview
        column={ COLUMN }
        objectId={ null }
      />
    )

    expect(screen.getByText('data-hub.column-config-modal.preview.placeholder')).toBeInTheDocument()
    expect(apiState.calls.gridPreview).toHaveLength(0)
  })

  test('AC3: BaseColumnEditor renders without throwing and requests no preview', () => {
    render(
      <BaseColumnEditor
        columns={ [{ key: 'name', fieldtype: 'input', type: 'dataobject.adapter' } as any, { key: 'Calc', fieldtype: 'advanced', type: 'dataobject.advanced' } as any] }
        entity='Product'
        onApply={ () => {} }
        onCancel={ () => {} }
        sourceFieldsRegistryId='source'
        transformersRegistryId='transformers'
      />
    )

    expect(apiState.calls.gridPreview).toHaveLength(0)
    expect(apiState.calls.grid).toHaveLength(0)
  })

  test('AC4: ColumnPipelineForm and ColumnEditorItemBody props accept objectId', () => {
    const form: Pick<ColumnPipelineFormProps, 'objectId'> = { objectId: 5 }
    const body: Pick<ColumnEditorItemBodyProps, 'objectId'> = { objectId: null }

    expect(form.objectId).toBe(5)
    expect(body.objectId).toBeNull()
    expect(ColumnPipelineForm).toBeDefined()
    expect(ColumnEditorItemBody).toBeDefined()
  })
})
