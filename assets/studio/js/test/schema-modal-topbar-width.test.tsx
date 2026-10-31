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

// The SDK's Toolbar draws the border (theme tokens, per position); the mock shows which position and theme it got
jest.mock('@pimcore/studio-ui-bundle/components', () => {
  const actual = jest.requireActual('./mocks/studio-ui-bundle/components')
  return {
    ...actual,
    // the full-width slot: no padding container around what goes in it
    ContentLayout: ({ renderTopBar, children }: any) => (
      <div>
        { renderTopBar !== undefined && <div data-testid='top-bar-slot'>{ renderTopBar }</div> }
        <div data-testid={ renderTopBar !== undefined ? 'padded-body' : undefined }>{ children }</div>
      </div>
    ),
    Toolbar: ({ children, position, theme, padding }: any) => (
      <div
        data-padding-x={ padding?.x }
        data-position={ position }
        data-testid='toolbar'
        data-theme={ theme }
      >
        { children }
      </div>
    )
  }
})

jest.mock('../src/modules/config/components/column-config-modal/column-pipeline-form', () => ({
  ColumnPipelineForm: () => <span />
}))

const modal = (): React.JSX.Element => (
  <ColumnConfigModal
    columns={ [] }
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
  apiState.available = { data: { columns: [] }, isLoading: false }
  apiState.grid = () => ({ data: { items: [{ id: 5, columns: [] }] } })
})

const expectFullWidth = (): void => {
  const bar = screen.getByTestId('preview-mode-header')
  expect(screen.getByTestId('top-bar-slot')).toContainElement(bar)
  expect(screen.getByTestId('padded-body')).not.toContainElement(bar)
  // no inner padding between the slot and the bar: the Toolbar's own padding is none on x
  expect(bar.closest('[data-testid="toolbar"]')).toHaveAttribute('data-padding-x', 'none')
}

describe('schema modal top bar width', () => {
  test('AC1: with preview mode off the top bar is in the full-width slot', () => {
    render(modal())
    expectFullWidth()
  })

  test('AC2: with preview mode on the top bar is in the full-width slot', () => {
    render(modal())
    fireEvent.click(screen.getByRole('switch'))
    expectFullWidth()
  })
})
