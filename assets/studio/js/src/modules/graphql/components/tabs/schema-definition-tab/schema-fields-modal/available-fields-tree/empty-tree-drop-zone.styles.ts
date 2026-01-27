/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ token, css }) => {
  return {
    dropZone: css`
      padding: ${token.paddingSM}px ${token.paddingXS}px;
      text-align: center;
      color: ${token.colorTextTertiary};
      border: 1px dashed ${token.colorBorder};
      border-radius: ${token.borderRadius}px;
      min-height: 100px;
    `
  }
})
