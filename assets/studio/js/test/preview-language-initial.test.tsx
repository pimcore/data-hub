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
import { PreviewHeader } from '../src/modules/config/components/column-config-modal/preview-header'
import { PreviewModeProvider, usePreviewMode } from '@pimcore/studio-ui-bundle/components'
import { resolvePreviewLanguage } from '../src/modules/config/components/column-config-modal/preview-language'

// The user's first content language is German
jest.mock('@pimcore/studio-ui-bundle/modules/auth', () => ({
  useUser: () => ({ contentLanguages: ['de', 'en'] })
}))

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
      { key: 'advanced', type: 'dataobject.advanced', localizable: true, config: {} }
    ]
  },
  isLoading: false
}

const COLUMNS = [
  { key: 'Calc', fieldtype: 'advanced', type: 'dataobject.advanced', localizable: true, config: { advancedColumns: [], transformers: [] } }
]

const modal = (language: string | undefined, onLanguageChange: (language: string) => void = () => {}): React.JSX.Element => (
  <ColumnConfigModal
    columns={ COLUMNS }
    entity='Product'
    language={ language }
    onApply={ () => {} }
    onCancel={ () => {} }
    onLanguageChange={ onLanguageChange }
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

const State = (): React.JSX.Element => <span data-testid='state'>{ usePreviewMode().language ?? 'unset' }</span>

beforeEach(() => {
  resetApiState()
  apiState.available = AVAILABLE
  apiState.grid = () => ({ data: { items: [{ id: 5, columns: [{ key: 'fullpath', value: '/P/S' }] }] } })
  apiState.gridPreview = () => ({ data: { value: [{ type: 'string', value: 'Calculated' }] } })
})

describe('preview language when none is saved', () => {
  test('AC1: the bar shows the first content language and the previews are requested in it', () => {
    render(modal(undefined))
    fireEvent.click(screen.getByRole('switch'))

    expect(screen.getByLabelText('preview-language')).toHaveValue('de')
    expect(apiState.calls.gridPreview.length).toBeGreaterThan(0)
    expect(apiState.calls.gridPreview.slice(-1)[0].body.column.locale).toBe('de')
  })

  test('AC1: the preview-mode state holds the first content language', () => {
    render(
      <PreviewModeProvider>
        <State />
        <PreviewHeader entity='Product' />
      </PreviewModeProvider>
    )

    expect(screen.getByTestId('state')).toHaveTextContent('de')
    expect(screen.getByLabelText('preview-language')).toHaveValue('de')
  })

  test('AC1: the helper falls back saved language, then first content language, then en', () => {
    expect(resolvePreviewLanguage('fr', ['de'])).toBe('fr')
    expect(resolvePreviewLanguage(undefined, ['de', 'en'])).toBe('de')
    expect(resolvePreviewLanguage(undefined, undefined)).toBe('en')
  })

  test('AC2: a saved language is used by the bar and the previews', () => {
    render(modal('en'))
    fireEvent.click(screen.getByRole('switch'))

    expect(screen.getByLabelText('preview-language')).toHaveValue('en')
    expect(apiState.calls.gridPreview.slice(-1)[0].body.column.locale).toBe('en')
  })

  test('AC3: seeding does not call onLanguageChange, a switch in the bar does', () => {
    const onLanguageChange = jest.fn()
    render(
      <PreviewModeProvider>
        <PreviewHeader
          entity='Product'
          onLanguageChange={ onLanguageChange }
        />
      </PreviewModeProvider>
    )

    expect(screen.getByLabelText('preview-language')).toHaveValue('de')
    expect(onLanguageChange).not.toHaveBeenCalled()

    fireEvent.change(screen.getByLabelText('preview-language'), { target: { value: 'en' } })
    expect(onLanguageChange).toHaveBeenCalledTimes(1)
    expect(onLanguageChange).toHaveBeenLastCalledWith('en')

    fireEvent.change(screen.getByLabelText('preview-language'), { target: { value: 'de' } })
    expect(onLanguageChange).toHaveBeenLastCalledWith('de')
  })
})
