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

/** the surface's own width: in a narrow change request tab the page's width says nothing about it */
export const SURFACE_CONTAINER = 'dh-graphql-review'

const RAIL_WIDTH = 320
const RAIL_GAP = 24
// the schema table and the editor's tabs need this much beside the rail; below it the editor goes under the list
const EDITOR_MIN_WIDTH = 400
export const EDITOR_BELOW_WIDTH = RAIL_WIDTH + RAIL_GAP + EDITOR_MIN_WIDTH
const NARROW = `@container ${SURFACE_CONTAINER} (max-width: ${EDITOR_BELOW_WIDTH - 1}px)`

export const useStyles = createStyles(({ token, css }) => ({
  frame: css`
    container-name: ${SURFACE_CONTAINER};
    container-type: inline-size;
  `,

  // the modal gives the surface its gutters; the split only divides what is inside them
  split: css`
    display: flex;
    align-items: stretch;
    flex: 1 1 auto;
    gap: ${RAIL_GAP}px;
    height: 70vh;
    min-height: 420px;
    ${NARROW} {
      flex-direction: column;
    }
  `,

  rail: css`
    width: ${RAIL_WIDTH}px;
    flex: 0 0 ${RAIL_WIDTH}px;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    /* the modal body already provides the left gutter; only the divider needs clearing */
    padding-right: ${token.paddingLG}px;
    border-right: 1px solid ${token.colorSplit};
    ${NARROW} {
      width: auto;
      flex: 0 1 auto;
      max-height: 40%;
      padding-right: 0;
      padding-bottom: ${token.paddingLG}px;
      border-right: none;
      border-bottom: 1px solid ${token.colorSplit};
    }
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
