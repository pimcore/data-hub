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
import { Popover } from 'antd'

interface InlineDropdownPanelProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  content: React.ReactNode
  children: React.ReactNode
}

/**
 * Anchored to its trigger and rendered in a portal so it is not clipped by
 * `overflow: hidden` ancestors (e.g. the bordered table accordion), and closes
 * on outside clicks.
 */
export const InlineDropdownPanel = ({ open, onOpenChange, content, children }: InlineDropdownPanelProps): React.JSX.Element => {
  return (
    <Popover
      arrow={ false }
      content={ content }
      onOpenChange={ onOpenChange }
      open={ open }
      placement="bottomLeft"
      trigger="click"
    >
      {children}
    </Popover>
  )
}
