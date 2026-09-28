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
import { ConfigSummary, type ConfigSummarySection } from '@pimcore/change-control-bundle/sdk'
import { type ConfigBrief } from './config-brief'

interface Props {
  readonly brief: ConfigBrief
  /** the section the editor is showing, so the reader knows where they are */
  readonly activeSection: string | undefined
  readonly onJump: (section: string) => void
}

/**
 * The rail for a GraphQL endpoint that does not exist yet: the same sections as the change rail,
 * opening the same tabs, but each says what it is set to rather than marking every field new.
 */
export const ConfigBriefCard: React.FC<Props> = ({ brief, activeSection, onJump }) => {
  const { t } = useTranslation()

  const sections: ConfigSummarySection[] = brief.sections.map((section) => ({
    key: section.key,
    label: t(section.label),
    rows: [{ key: section.key, label: section.value, note: section.note }]
  }))

  return (
    <ConfigSummary
      active={ brief.active }
      activeKey={ activeSection }
      description={ brief.description }
      name={ brief.name }
      onOpenSection={ onJump }
      sections={ sections }
      variant="description"
    />
  )
}
