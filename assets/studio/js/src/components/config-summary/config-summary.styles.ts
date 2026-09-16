/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

// antd-style imported directly resolves to this package's copy, outside Studio's theme
import { createStyles } from '@pimcore/studio-ui-bundle/app'

export const useStyles = createStyles(({ token, css }) => ({
  /* min-width 0 throughout: the ellipses need it inside the rail's flex */
  summary: css`
    min-width: 0;
  `,
  head: css`
    border-bottom: 1px solid ${token.colorSplit};
  `,
  name: css`
    min-width: 0;
    font-size: ${token.fontSizeLG}px;
  `,
  pill: css`
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
  description: css`
    line-height: ${token.lineHeightSM};
    color: ${token.colorTextSecondary};
    overflow-wrap: anywhere;
  `,
  spine: css`
    display: grid;
    grid-template-columns: ${token.marginXS}px minmax(0, 1fr);
    column-gap: ${token.marginSM}px;
  `,
  /* (line box - node) / 2 centres the node on the heading's first line */
  mark: css`
    padding-top: ${(token.fontSizeSM * token.lineHeightSM - 7) / 2}px;
  `,
  node: css`
    flex: none;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: ${token.colorPrimary};
  `,
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
  caption: css`
    font-size: ${token.fontSizeSM}px;
    font-weight: ${token.fontWeightStrong};
    letter-spacing: .07em;
    text-transform: uppercase;
    line-height: ${token.lineHeightSM};
  `,
  role: css`
    color: ${token.colorTextTertiary};
  `,
  /* button reset; the font shorthand would undo caption */
  section: css`
    width: fit-content;
    max-width: 100%;
    margin: 0 0 ${token.marginXXS}px;
    padding: 0;
    border: none;
    background: none;
    font-family: inherit;
    color: ${token.colorPrimary};
    cursor: pointer;

    &:hover {
      color: ${token.colorPrimaryHover};
    }
  `,
  value: css`
    font-weight: ${token.fontWeightStrong};
    font-variant-numeric: tabular-nums;
    overflow-wrap: anywhere;
  `,
  note: css`
    font-size: ${token.fontSizeSM}px;
    line-height: ${token.lineHeightSM};
    color: ${token.colorTextTertiary};
    overflow-wrap: anywhere;
  `,
  field: css`
    min-height: ${token.controlHeightSM}px;

    /* the marks are tall; without a gap the rows read as one block */
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
  `
}))
