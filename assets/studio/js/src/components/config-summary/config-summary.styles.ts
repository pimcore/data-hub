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
 * The summary's own look, for both of its variants: a changed configuration lists what moved,
 * a new one tells what it does, and neither should look like a different product. Studio has
 * no primitive for a marked-up spine or a caption that behaves as a button, so these stay
 * hand-drawn.
 */
export const useStyles = createStyles(({ token, css }) => ({
  /* flush in the rail rather than a box inside it: the rail's own edge does the separating */
  summary: css`
    min-width: 0;
  `,
  head: css`
    display: flex;
    flex-direction: column;
    gap: ${token.marginXXS}px;
    padding: ${token.paddingXS}px 0 ${token.paddingSM}px;
    border-bottom: 1px solid ${token.colorSplit};
  `,
  identity: css`
    display: flex;
    align-items: center;
    gap: ${token.marginXS}px;
  `,
  name: css`
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: ${token.fontSizeLG}px;
    font-weight: ${token.fontWeightStrong};
    color: ${token.colorText};
  `,
  /* the on/off switch of the whole pipeline, said once and quietly */
  pill: css`
    flex: none;
    display: inline-flex;
    align-items: center;
    gap: ${token.marginXXS}px;
    height: ${token.controlHeightSM - 2}px;
    padding: 0 ${token.paddingXS}px;
    border-radius: ${token.borderRadiusSM}px;
    background: ${token.colorFillTertiary};
    color: ${token.colorTextSecondary};
    font-size: ${token.fontSize}px;
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
  description: css`
    font-size: ${token.fontSize}px;
    line-height: ${token.lineHeightSM};
    color: ${token.colorTextSecondary};
    overflow-wrap: anywhere;
  `,
  /* a dotted spine down the left, one node per section or per step of the pipeline */
  spine: css`
    display: grid;
    grid-template-columns: ${token.marginXS}px minmax(0, 1fr);
    column-gap: ${token.marginSM}px;
    padding: ${token.padding}px 0;
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
    padding-bottom: ${token.padding}px;
  `,
  entryLast: css`
    min-width: 0;
  `,
  /* the label of a step: what part it plays, not something to press */
  role: css`
    font-size: ${token.fontSizeSM}px;
    font-weight: ${token.fontWeightStrong};
    letter-spacing: .07em;
    text-transform: uppercase;
    color: ${token.colorTextTertiary};
    line-height: ${token.lineHeightSM};
  `,
  /* the label of a section: the one control in the card, it opens the section in the editor */
  section: css`
    /* block-level, not inline: an inline button rides the entry's baseline and lands a few
       pixels below the row it is meant to head */
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
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: ${token.marginXS}px;
    min-height: ${token.controlHeightSM}px;

    /* the marks are tall for their row; without this they read as one block */
    & + & {
      margin-top: ${token.marginXXS}px;
    }
  `,
  fieldLabel: css`
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-weight: ${token.fontWeightStrong};
    color: ${token.colorText};
  `,
  foot: css`
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: ${token.paddingSM}px 0;
    border-top: 1px solid ${token.colorSplit};
    font-variant-numeric: tabular-nums;
  `,
  footLead: css`
    font-size: ${token.fontSize}px;
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
