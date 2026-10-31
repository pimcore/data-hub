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

// The SDK's Toolbar draws the border (theme tokens, per position); the mock shows which position and theme it got
jest.mock('@pimcore/studio-ui-bundle/components', () => {
  const actual = jest.requireActual('./mocks/studio-ui-bundle/components')
  return {
    ...actual,
    Toolbar: ({ children, position, theme }: any) => (
      <div
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

const expectGridModalBorder = (): void => {
  const bar = within(screen.getByTestId('preview-mode-header')).queryByTestId('toolbar')
    ?? screen.getByTestId('preview-mode-header').closest('[data-testid="toolbar"]')
  expect(bar).not.toBeNull()
  // the grid modal's top bar: <Toolbar position="content" theme="secondary">
  expect(bar).toHaveAttribute('data-position', 'content')
  expect(bar).toHaveAttribute('data-theme', 'secondary')
}

describe('schema modal top bar', () => {
  test('AC1: with preview mode off the top bar has the Grid Configuration modal\'s border', () => {
    render(modal())
    expectGridModalBorder()
  })

  test('AC2: with preview mode on the top bar has the Grid Configuration modal\'s border', () => {
    render(modal())
    fireEvent.click(screen.getByRole('switch'))
    expectGridModalBorder()
  })
})
