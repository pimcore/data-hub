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
    panel: css`
      position: absolute;
      z-index: ${token.zIndexPopupBase};
      min-width: 400px;
      background-color: ${token.colorBgContainer};
      padding: ${token.padding}px;
      box-shadow: ${token.boxShadowSecondary};
      border-radius: ${token.borderRadius}px;
    `
  }
})
