/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

// the host's createStyles, as Studio's own components use it - antd-style imported directly
// here resolves to this package's own copy, outside Studio's ThemeProvider, so `token` would
// be antd's stock palette and the primary the wrong blue
import { createStyles } from '@pimcore/studio-ui-bundle/app'

/**
 * What no Studio primitive draws: the spine and its nodes, the pill, a caption that behaves
 * as a button, and the small-caps typography. Layout, spacing, weight and the secondary colour
 * are Flex, Box and Text in the component - their size names resolve to the same tokens.
 */
export const useStyles = createStyles(({ token, css }) => ({
  /* flush in the rail rather than a box inside it: the rail's own edge does the separating */
  summary: css`
    min-width: 0;
  `,
  head: css`
    border-bottom: 1px solid ${token.colorSplit};
  `,
  /* the one thing Text cannot say about the name: it is a size up */
  name: css`
    min-width: 0;
    font-size: ${token.fontSizeLG}px;
  `,
  /* the on/off switch of the configuration, said once and quietly */
  pill: css`
    flex: none;
    height: ${token.controlHeightSM - 2}px;
    padding: 0 ${token.paddingXS}px;
    border-radius: ${token.borderRadiusSM}px;
    background: ${token.colorFillTertiary};
    color: ${token.colorTextSecondary};
    line-height: 1;
  `,
  dot: css`
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: ${token.colorTextQuaternary};
  `,
  dotOn: css`
    background: ${token.colorSuccess};
  `,
  /* tighter than body text, so a two-line description reads as one thing */
  description: css`
    line-height: ${token.lineHeightSM};
    color: ${token.colorTextSecondary};
    overflow-wrap: anywhere;
  `,
  /* a dotted spine down the left, one node per section of the configuration */
  spine: css`
    display: grid;
    grid-template-columns: ${token.marginXS}px minmax(0, 1fr);
    column-gap: ${token.marginSM}px;
  `,
  /* the node sits on the centre of the heading's first line, whatever the theme makes of the
     small font: (line box - node) / 2 rather than a fixed margin */
  mark: css`
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: ${(token.fontSizeSM * token.lineHeightSM - 7) / 2}px;
  `,
  node: css`
    flex: none;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: ${token.colorPrimary};
  `,
  /* a section the editor is not showing: the same node, hollow */
  nodeMuted: css`
    background: ${token.colorBgContainer};
    box-shadow: inset 0 0 0 1px ${token.colorPrimaryBorder};
  `,
  line: css`
    flex: 1;
    width: 1px;
    margin: ${token.marginXXS}px 0;
    background: ${token.colorPrimaryBorder};
  `,
  entry: css`
    min-width: 0;
  `,
  /* the label of a stop: what part it plays, not something to press */
  role: css`
    font-size: ${token.fontSizeSM}px;
    font-weight: ${token.fontWeightStrong};
    letter-spacing: .07em;
    text-transform: uppercase;
    color: ${token.colorTextTertiary};
    line-height: ${token.lineHeightSM};
  `,
  /* the label of a section: the one control in the card, it opens the section in the editor.
     Block-level, not inline: an inline button rides the entry's baseline and lands a few
     pixels below the row it is meant to head */
  section: css`
    display: flex;
    width: fit-content;
    align-items: center;
    gap: ${token.marginXXS}px;
    max-width: 100%;
    margin: 0 0 ${token.marginXXS}px;
    padding: 0;
    border: none;
    background: none;
    font: inherit;
    font-size: ${token.fontSizeSM}px;
    font-weight: ${token.fontWeightStrong};
    letter-spacing: .07em;
    text-transform: uppercase;
    line-height: ${token.lineHeightSM};
    color: ${token.colorPrimary};
    cursor: pointer;

    &:hover {
      color: ${token.colorPrimaryHover};
    }
  `,
  value: css`
    font-weight: ${token.fontWeightStrong};
    color: ${token.colorText};
    font-variant-numeric: tabular-nums;
    overflow-wrap: anywhere;
  `,
  note: css`
    font-size: ${token.fontSizeSM}px;
    line-height: ${token.lineHeightSM};
    color: ${token.colorTextTertiary};
    overflow-wrap: anywhere;
  `,
  /* a changed field: its name, and what became of it */
  field: css`
    min-height: ${token.controlHeightSM}px;

    /* the marks are tall for their row; without this they read as one block */
    & + & {
      margin-top: ${token.marginXXS}px;
    }
  `,
  fieldLabel: css`
    min-width: 0;
  `,
  foot: css`
    border-top: 1px solid ${token.colorSplit};
    font-variant-numeric: tabular-nums;
  `,
  footLead: css`
    line-height: ${token.lineHeightSM};
    color: ${token.colorTextSecondary};

    b {
      color: ${token.colorText};
    }
  `,
  footGroups: css`
    font-size: ${token.fontSizeSM}px;
    line-height: ${token.lineHeightSM};
    color: ${token.colorTextTertiary};
  `
}))
