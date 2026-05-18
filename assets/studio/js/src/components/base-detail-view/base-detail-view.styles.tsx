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
    formWrapper: css`
      display: contents;

      > form {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
      }
    `
  }
})
