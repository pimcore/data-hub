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
import {
  Badge, Divider, Flex, Icon, Tag, Text, VerticalTimeline
} from '@pimcore/studio-ui-bundle/components'
import { StatusTag, type ChangeStatus } from './status-tag'
import { useStyles } from './config-change-summary.styles'

/** one field of the configuration that differs */
export interface ConfigChangeField {
  readonly key: string
  /** what the editor calls this field */
  readonly label: string
  /** the full address, shown on hover; the label alone is often ambiguous */
  readonly hint?: string
  readonly status?: ChangeStatus
}

/** a part of the configuration, as the editor groups it - usually one tab */
export interface ConfigChangeSection {
  readonly key: string
  readonly label: string
  readonly fields: ConfigChangeField[]
}

export interface ConfigChangeSummaryProps {
  /** the configuration this is about */
  readonly name: string
  readonly description?: string
  /** whether the configuration is switched on, which a set of changes never says on its own */
  readonly active?: boolean
  readonly sections: ConfigChangeSection[]
  /** the section the editor is currently showing */
  readonly activeKey?: string
  /** omit to render the section labels as captions rather than as controls */
  readonly onOpenSection?: (key: string) => void
}

const ARROW = { width: 12, height: 12 }

const T = 'data-hub.review'

/**
 * What a set of proposed changes does to one Data Hub configuration: the configuration it is
 * about, then Studio's vertical timeline with a node per section that differs.
 *
 * Presentation over data only. It knows nothing about how a change is stored, reviewed or
 * approved, which is what lets every adapter show the same summary without agreeing on
 * anything beyond these two shapes. It decides nothing either - the only control is a section
 * label, which opens that section in the editor.
 */
export const ConfigChangeSummary: React.FC<ConfigChangeSummaryProps> = ({
  name, description, active, sections, activeKey, onOpenSection
}) => {
  const { t } = useTranslation()
  const { styles } = useStyles()

  // counted here rather than passed in, so two adapters cannot disagree about what they count
  const changed = sections.reduce((total, section) => total + section.fields.length, 0)

  const heading = (section: ConfigChangeSection): React.ReactNode => {
    const label = <span className={ styles.sectionLabel }>{ section.label }</span>

    if (onOpenSection === undefined) {
      return label
    }

    return (
      <button
        className={ styles.section }
        onClick={ () => { onOpenSection(section.key) } }
        title={ t(`${T}.open-section`) }
        type="button"
      >
        { label }
        <Icon
          options={ ARROW }
          value="arrow-narrow-right"
        />
      </button>
    )
  }

  return (
    <div className={ styles.summary }>
      <Flex
        align="center"
        gap="small"
      >
        <Text
          ellipsis
          strong
        >
          { name }
        </Text>
        { active !== undefined && (
          <Badge
            color={ active ? 'green' : 'default' }
            text={ t(active ? `${T}.active` : `${T}.inactive`) }
          />
        ) }
        <Tag
          color="gold"
          style={ { marginInlineEnd: 0 } }
        >
          { t(changed === 1 ? `${T}.changed-field` : `${T}.changed-fields`).replace('%s', String(changed)) }
        </Tag>
      </Flex>

      { description !== undefined && description !== '' && (
        <Text type="secondary">{ description }</Text>
      ) }

      <Divider />

      <VerticalTimeline
        timeStamps={ sections.map((section) => (
          <div
            className={ section.key === activeKey ? 'is-active' : undefined }
            key={ section.key }
          >
            { heading(section) }
            { section.fields.map((field) => (
              <Flex
                align="center"
                gap="small"
                justify="space-between"
                key={ field.key }
                title={ field.hint }
              >
                <Text
                  ellipsis
                  strong
                >
                  { field.label }
                </Text>
                { field.status !== undefined && <StatusTag status={ field.status } /> }
              </Flex>
            )) }
          </div>
        )) }
      />
    </div>
  )
}
