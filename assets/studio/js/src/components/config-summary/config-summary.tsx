/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React from 'react'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { Icon, Tag } from '@pimcore/studio-ui-bundle/components'
import { StatusTag, type ChangeStatus } from './status-tag'
import { useStyles } from './config-summary.styles'

/**
 * One line of a section.
 *
 * Both of a configuration's summaries are the same shape: a primary line, an optional second
 * line, and an optional mark. Describing a change, the primary line is the field's name and
 * the mark says what became of it; describing a configuration, the primary line is the value
 * and there is no mark.
 */
export interface ConfigSummaryRow {
  readonly key: string
  readonly label: string
  /** a second, quieter line - what the value means, or where it comes from */
  readonly note?: string
  /** the full address, shown on hover; a label alone is often ambiguous */
  readonly hint?: string
  /** omit for a row that states a fact rather than a change */
  readonly status?: ChangeStatus
}

/** a part of the configuration, as the editor groups it - usually one tab or one step */
export interface ConfigSummarySection {
  readonly key: string
  readonly label: string
  readonly rows: ConfigSummaryRow[]
}

/**
 * The line under the spine: how much is waiting in the editor, said once. `lead` may mark its
 * own emphasis with `**…**`, so a translator decides what carries it.
 */
export interface ConfigSummaryFoot {
  readonly lead: string
  readonly detail?: string
}

/**
 * `changes` counts what moved; `description` says what the configuration is, for one that
 * does not exist yet - every leaf of it would be "added", so counting says only "all of it".
 */
export type ConfigSummaryVariant = 'changes' | 'description'

export interface ConfigSummaryProps {
  /** the configuration this is about */
  readonly name: string
  readonly description?: string
  /** whether it is switched on; omit where the summary should not say */
  readonly active?: boolean
  readonly sections: ConfigSummarySection[]
  readonly variant?: ConfigSummaryVariant
  readonly foot?: ConfigSummaryFoot
  /** the section the editor is currently showing */
  readonly activeKey?: string
  /** omit to render the section labels as captions rather than as controls */
  readonly onOpenSection?: (key: string) => void
}

const ARROW = { width: 12, height: 12 }

const T = 'data-hub.review'

/** `**…**` in a translated string is the translator's emphasis */
const emphasised = (value: string): React.ReactNode[] =>
  value.split('**').map((part, index) => (
    index % 2 === 1 ? <b key={ `b${index}` }>{ part }</b> : <React.Fragment key={ `t${index}` }>{ part }</React.Fragment>
  ))

/**
 * What there is to know about one Data Hub configuration before acting on it: the
 * configuration itself, then a dotted spine with a node per section.
 *
 * Presentation over data only. It knows nothing about how a change is stored, reviewed or
 * approved, which is what lets every adapter show the same summary without agreeing on
 * anything beyond these shapes. It decides nothing either - the only control is a section
 * label, which opens that section in the editor.
 */
export const ConfigSummary: React.FC<ConfigSummaryProps> = ({
  name, description, active, sections, variant = 'changes', foot, activeKey, onOpenSection
}) => {
  const { t } = useTranslation()
  const { styles, cx } = useStyles()

  // counted here rather than passed in, so two adapters cannot disagree about what they count
  const rows = sections.reduce((total, section) => total + section.rows.length, 0)

  const heading = (section: ConfigSummarySection): React.ReactNode => {
    if (onOpenSection === undefined) {
      return <div className={ styles.role }>{ section.label }</div>
    }

    return (
      <button
        className={ styles.section }
        onClick={ () => { onOpenSection(section.key) } }
        title={ t(`${T}.open-section`) }
        type="button"
      >
        <span>{ section.label }</span>
        <Icon
          options={ ARROW }
          value="arrow-narrow-right"
        />
      </button>
    )
  }

  // a change is a name and a mark on one line; a fact is a value with its note beneath
  const row = (item: ConfigSummaryRow): React.ReactNode => variant === 'description'
    ? (
      <React.Fragment key={ item.key }>
        <div
          className={ styles.value }
          title={ item.hint }
        >
          { item.label }
        </div>
        { item.note !== undefined && item.note !== '' && <div className={ styles.note }>{ item.note }</div> }
      </React.Fragment>
      )
    : (
      <div
        className={ styles.field }
        key={ item.key }
        title={ item.hint }
      >
        <span className={ styles.fieldLabel }>{ item.label }</span>
        { item.status !== undefined && <StatusTag status={ item.status } /> }
      </div>
      )

  const pill = active !== undefined && (
    <span className={ styles.pill }>
      <span className={ cx(styles.dot, active && styles.dotOn) } />
      { t(active ? `${T}.active` : `${T}.inactive`) }
    </span>
  )

  return (
    <div className={ styles.summary }>
      <div className={ styles.head }>
        <div className={ styles.identity }>
          <span
            className={ styles.name }
            title={ name }
          >
            { name }
          </span>
          { variant === 'description'
            ? (
              <>
                <Tag
                  color="green"
                  style={ { marginInlineEnd: 0 } }
                >
                  { t(`${T}.new`) }
                </Tag>
                { pill }
              </>
              )
            : (
              <>
                { pill }
                <Tag
                  color="gold"
                  style={ { marginInlineEnd: 0 } }
                >
                  { t(rows === 1 ? `${T}.changed-field` : `${T}.changed-fields`).replace('%s', String(rows)) }
                </Tag>
              </>
              ) }
        </div>
        { description !== undefined && description !== '' && (
          <div className={ styles.description }>{ description }</div>
        ) }
      </div>

      <div className={ styles.spine }>
        { sections.map((section, index) => (
          <React.Fragment key={ section.key }>
            <div className={ styles.mark }>
              <span className={ cx(styles.node, section.key !== activeKey && styles.nodeMuted) } />
              { index < sections.length - 1 && <span className={ styles.line } /> }
            </div>
            <div className={ index === sections.length - 1 ? styles.entryLast : styles.entry }>
              { heading(section) }
              { section.rows.map(row) }
            </div>
          </React.Fragment>
        )) }
      </div>

      { foot !== undefined && (
        <div className={ styles.foot }>
          <div className={ styles.footLead }>{ emphasised(foot.lead) }</div>
          { foot.detail !== undefined && foot.detail !== '' && (
            <div className={ styles.footGroups }>{ foot.detail }</div>
          ) }
        </div>
      ) }
    </div>
  )
}
