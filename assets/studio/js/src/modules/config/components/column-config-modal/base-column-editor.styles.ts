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
  body: css`
    display: flex;
    gap: ${token.marginSM}px;
    height: 100%;
  `,
  fieldsPanel: css`
    display: flex;
    flex-direction: column;
    gap: ${token.marginXS}px;
    width: 280px;
    height: 100%;
    padding-right: ${token.paddingSM}px;
    border-right: 1px solid ${token.colorBorderSecondary};
  `,
  list: css`
    flex: 1;
    min-width: 0;
    height: 100%;
    overflow-y: auto;
  `
}))
