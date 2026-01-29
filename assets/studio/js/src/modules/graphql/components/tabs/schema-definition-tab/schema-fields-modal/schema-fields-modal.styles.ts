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
    contentLayout: css`
      height: 60vh;
      border-top: 1px solid ${token.colorBorderSecondary};
      border-bottom: 1px solid ${token.colorBorderSecondary};
      
      .pimcore-content-layout__content {
        padding-right: ${token.padding}px;
      }
    `,

    gridContainer: css`
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: ${token.marginXS}px;
      width: 100%;
      align-items: stretch;
    `
  }
})
