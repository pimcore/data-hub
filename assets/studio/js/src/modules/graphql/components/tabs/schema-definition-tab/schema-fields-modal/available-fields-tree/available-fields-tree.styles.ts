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
    treeContainer: css`
      .ant-tree-title {
        white-space: nowrap;
      }
      
      .ant-tree-list-holder-inner .ant-tree-treenode {
        padding: 1px ${token.paddingXS}px;
      }
    `
  }
})
