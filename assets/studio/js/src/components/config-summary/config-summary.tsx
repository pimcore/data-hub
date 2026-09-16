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
import { Box, Flex, Icon, Tag, Text } from '@pimcore/studio-ui-bundle/components'
import { StatusTag, type ChangeStatus } from './status-tag'
import { useStyles } from './config-summary.styles'

/** one line of a section: a label, an optional second line, an optional mark */
export interface ConfigSummaryRow {
  readonly key: string
  readonly label: string
  /** quieter second line */
  readonly note?: string
  /** shown on hover, e.g. the full path */
  readonly hint?: string
  /** omit for a fact rather than a change; only rows with a status are counted */
  readonly status?: ChangeStatus
}

/** a part of the configuration as the editor groups it, usually a tab */
export interface ConfigSummarySection {
  readonly key: string
  readonly label: string
  readonly rows: ConfigSummaryRow[]
}

/** the line under the spine; `lead` may mark emphasis with `**…**` */
export interface ConfigSummaryFoot {
  readonly lead: string
  readonly detail?: string
}

/** `changes` counts what moved; `description` describes a configuration that does not exist yet */
export type ConfigSummaryVariant = 'changes' | 'description'

export interface ConfigSummaryProps {
  readonly name: string
  readonly description?: string
  /** omit to show no on/off state */
  readonly active?: boolean
  readonly sections: ConfigSummarySection[]
  readonly variant?: ConfigSummaryVariant
  readonly foot?: ConfigSummaryFoot
  /** the section the editor is currently showing */
  readonly activeKey?: string
  /** omit to render section labels as captions rather than buttons */
  readonly onOpenSection?: (key: string) => void
}

const ARROW = { width: 12, height: 12 }

// FlexProps has no button attributes; without type=button a surrounding form would submit
const BUTTON = { type: 'button' }

const T = 'data-hub.review'

const emphasised = (value: string): React.ReactNode[] =>
  value.split('**').map((part, index) => (
    index % 2 === 1 ? <b key={ `b${index}` }>{ part }</b> : <React.Fragment key={ `t${index}` }>{ part }</React.Fragment>
  ))

/**
 * One Data Hub configuration in brief: the configuration, then a spine with a node per section.
 * Presentation only; the section label is the only control.
 */
export const ConfigSummary: React.FC<ConfigSummaryProps> = ({
  name, description, active, sections, variant = 'changes', foot, activeKey, onOpenSection
}) => {
  const { t } = useTranslation()
  const { styles, cx } = useStyles()

  // counted here so adapters cannot disagree about what counts
  const changes = sections.reduce((total, section) => total + section.rows.filter((row) => row.status !== undefined).length, 0)

  const heading = (section: ConfigSummarySection): React.ReactNode => {
    if (onOpenSection === undefined) {
      return <div className={ cx(styles.caption, styles.role) }>{ section.label }</div>
    }

    return (
      <Flex
        align="center"
        className={ cx(styles.caption, styles.section) }
        component="button"
        gap="mini"
        onClick={ () => { onOpenSection(section.key) } }
        title={ t(`${T}.open-section`) }
        { ...BUTTON }
      >
        <span>{ section.label }</span>
        <Icon
          options={ ARROW }
          value="arrow-narrow-right"
        />
      </Flex>
    )
  }

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
      <Flex
        align="center"
        className={ styles.field }
        gap="extra-small"
        justify="space-between"
        key={ item.key }
        title={ item.hint }
      >
        <Text
          className={ styles.fieldLabel }
          ellipsis
          strong
        >
          { item.label }
        </Text>
        { item.status !== undefined && <StatusTag status={ item.status } /> }
      </Flex>
      )

  const pill = active !== undefined && (
    <Flex
      align="center"
      className={ styles.pill }
      flex="none"
      gap="mini"
    >
      <span className={ cx(styles.dot, active && styles.dotOn) } />
      { t(active ? `${T}.active` : `${T}.inactive`) }
    </Flex>
  )

  return (
    <div className={ styles.summary }>
      <Box
        className={ styles.head }
        padding={ { top: 'extra-small', bottom: 'small' } }
      >
        <Flex
          gap="mini"
          vertical
        >
          <Flex
            align="center"
            gap="extra-small"
          >
            <Text
              className={ styles.name }
              ellipsis
              strong
              title={ name }
            >
              { name }
            </Text>
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
                    { t(changes === 1 ? `${T}.changed-field` : `${T}.changed-fields`).replace('%s', String(changes)) }
                  </Tag>
                </>
                ) }
          </Flex>
          { description !== undefined && description !== '' && (
            <Text className={ styles.description }>{ description }</Text>
          ) }
        </Flex>
      </Box>

      <Box
        className={ styles.spine }
        padding={ { y: 'normal' } }
      >
        { sections.map((section, index) => {
          const last = index === sections.length - 1

          return (
            <React.Fragment key={ section.key }>
              <Flex
                align="center"
                className={ styles.mark }
                vertical
              >
                <span className={ cx(styles.node, section.key !== activeKey && styles.nodeMuted) } />
                { !last && <span className={ styles.line } /> }
              </Flex>
              <Box
                className={ styles.entry }
                padding={ last ? undefined : { bottom: 'normal' } }
              >
                { heading(section) }
                { section.rows.map(row) }
              </Box>
            </React.Fragment>
          )
        }) }
      </Box>

      { foot !== undefined && (
        <Box
          className={ styles.foot }
          padding={ { y: 'small' } }
        >
          <Flex
            gap={ 2 }
            vertical
          >
            <Text className={ styles.footLead }>{ emphasised(foot.lead) }</Text>
            { foot.detail !== undefined && foot.detail !== '' && (
              <div className={ styles.note }>{ foot.detail }</div>
            ) }
          </Flex>
        </Box>
      ) }
    </div>
  )
}
