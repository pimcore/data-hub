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

export const useStyles = createStyles(({ css, token }) => {
  return {
    divider: css`
      height: ${token.fontSizeLG}px;
      align-self: center;
      margin-top: 0 !important;
      margin-bottom: 0 !important;
      margin-inline: ${token.marginXS}px;
    `
  }
})
