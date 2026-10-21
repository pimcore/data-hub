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
    panes: css`
      height: 60vh;
      border-bottom: 1px solid ${token.colorBorderSecondary};
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
