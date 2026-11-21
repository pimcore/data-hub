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

export const useStyles = createStyles(({ token, css }) => ({
  // the modal gives the surface its gutters; the split only divides what is inside them
  split: css`
    display: flex;
    align-items: stretch;
    flex: 1 1 auto;
    gap: ${token.paddingLG}px;
    height: 70vh;
    min-height: 420px;
  `,

  rail: css`
    width: 320px;
    flex: 0 0 320px;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    /* the modal body already provides the left gutter; only the divider needs clearing */
    padding-right: ${token.paddingLG}px;
    border-right: 1px solid ${token.colorSplit};
  `,

  editor: css`
    flex: 1 1 auto;
    min-width: 0;
    min-height: 0;
    display: flex;
    flex-direction: column;
  `,

  empty: css`
    color: ${token.colorTextTertiary};
    font-size: ${token.fontSizeSM}px;
    padding: ${token.paddingXS}px 0;
  `
}))
