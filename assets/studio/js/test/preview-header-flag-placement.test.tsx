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
import { PreviewHeader } from '../src/modules/config/components/column-config-modal/preview-header'

let previewEnabled = false

// The mock shows the style the row's Flex got and which components are its direct children
jest.mock('@pimcore/studio-ui-bundle/components', () => ({
  Flex: ({ children, style, 'data-testid': testId }: any) => (
    <div
      data-testid={ testId }
      data-width={ style?.width }
      data-min-width={ style?.minWidth }
    >{ children }</div>
  ),
  Toolbar: ({ children }: any) => <div>{ children }</div>,
  PreviewModeSwitch: () => <span data-testid='switch' />,
  PreviewModeBar: () => <span data-testid='bar' />,
  usePreviewMode: () => ({ enabled: previewEnabled, language: 'en', setLanguage: () => {} })
}))

describe('preview header flag placement', () => {
  test('AC1: a full-width Flex holds the switch and the bar as direct children, no wrapper div', () => {
    render(<PreviewHeader entity='Product' />)

    const header = screen.getByTestId('preview-mode-header')
    expect(header).toHaveAttribute('data-width', '100%')
    expect(Array.from(header.children).map(child => child.getAttribute('data-testid'))).toEqual(['switch', 'bar'])
  })

  test('AC7: the row may shrink (minWidth 0) so a long item name does not push the flag off', () => {
    render(<PreviewHeader entity='Product' />)

    expect(screen.getByTestId('preview-mode-header')).toHaveAttribute('data-min-width', '0')
  })

  test('AC2: no divider below the row while preview mode is on', () => {
    previewEnabled = true
    render(<PreviewHeader entity='Product' />)

    expect(screen.queryByTestId('preview-mode-header-divider')).not.toBeInTheDocument()
    previewEnabled = false
  })
})
