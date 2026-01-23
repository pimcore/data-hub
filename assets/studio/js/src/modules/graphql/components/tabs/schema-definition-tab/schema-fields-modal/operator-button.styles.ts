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

export const useOperatorButtonStyles = createStyles(({ token, css }) => {
  return {
    gridContainer: css`
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: ${token.marginXS}px;
      width: 100%;
      align-items: stretch;
    `,

    operatorButton: css`
      width: 100%;
      height: 100%;
      padding: ${token.paddingXS}px;
      border: 1px solid ${token.colorBorder};
      border-radius: ${token.borderRadius}px;
      background: ${token.colorBgContainer};
      cursor: pointer;

      &:hover {
        border-color: ${token.colorPrimary};
      }

      &:active {
        border-color: ${token.colorPrimary};
      }
    `,

    operatorIcon: css`
      color: ${token.colorTextSecondary};
      margin-bottom: ${token.marginXS}px;
    `,

    operatorName: css`
      text-align: center;
      color: ${token.colorTextSecondary};
      white-space: normal;
      word-wrap: break-word;
      overflow-wrap: break-word;
      max-width: 100%;
      font-size: 12px;
    `
  }
})
