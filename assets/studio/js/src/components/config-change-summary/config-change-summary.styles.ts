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

/**
 * What is left once Studio's own primitives carry the summary: the head is a Flex of Text and
 * Tag, the spine is VerticalTimeline, the state is a Badge. Only the section label has no
 * primitive - it has to read as a caption while behaving as a button.
 */
export const useStyles = createStyles(({ token, css }) => ({
  /* flush in whatever column holds it: that column's own edge does the separating */
  summary: css`
    min-width: 0;
  `,

  sectionLabel: css`
    font-size: ${token.fontSizeSM}px;
    font-weight: ${token.fontWeightStrong};
    letter-spacing: .07em;
    text-transform: uppercase;
    line-height: ${token.lineHeightSM};
  `,

  section: css`
    display: inline-flex;
    align-items: center;
    gap: ${token.marginXXS}px;
    max-width: 100%;
    margin: 0 0 ${token.marginXXS}px;
    padding: 0;
    border: none;
    background: none;
    font: inherit;
    color: ${token.colorPrimary};
    cursor: pointer;

    &:hover {
      color: ${token.colorPrimaryHover};
    }
  `
}))
