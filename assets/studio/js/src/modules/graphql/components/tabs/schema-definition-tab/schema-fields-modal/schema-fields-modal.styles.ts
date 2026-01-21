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

export const useStyles = createStyles(({ css }) => {
  return {
    tabs: css`
      .ant-tabs-nav {
        margin: 0;
      }
      
      .ant-tabs-nav-list {
        margin: 0;
      }
      
      .ant-tabs-tab {
        margin: 4px !important;
      }
      
      .ant-tabs-content, .ant-tabs-tabpane {
        height: 100%;
      }
    `
  }
})
