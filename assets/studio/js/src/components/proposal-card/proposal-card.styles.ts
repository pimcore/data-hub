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
 * One card shell for every adapter's proposal rail: a changed configuration lists what moved,
 * a new one tells what it will do, and neither should look like a different product.
 */
export const useStyles = createStyles(({ token, css }) => ({
  /* flush in the rail rather than a box inside it: the rail's own edge does the separating */
  card: css`
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
  /* whether the configuration is switched on, said once and quietly */
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
  /* a dotted spine down the left, one node per section of the configuration */
  spine: css`
    display: grid;
    grid-template-columns: ${token.marginXS}px minmax(0, 1fr);
    column-gap: ${token.marginSM}px;
    padding: ${token.padding}px 0;
  `,
  mark: css`
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: ${token.marginXXS}px;
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
    display: inline-flex;
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
