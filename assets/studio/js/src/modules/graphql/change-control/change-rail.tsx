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
import { type BackendConfiguration } from '../components/backend-types'
import { type ChangeGroup, type ConfigChange } from './config-review-model'
import { ConfigSummary, type ConfigSummarySection } from '@pimcore/change-control-bundle/sdk'

interface Props {
  readonly configuration: BackendConfiguration
  readonly groups: ChangeGroup[]
  /** the section the editor currently shows, so the reader knows where they are */
  readonly activeSection?: string
  readonly labelFor: (change: ConfigChange) => string
  readonly onJump: (section: string) => void
}

/**
 * The GraphQL configuration's changes, as the shared summary reads them. Everything this does
 * is translate the review model into the two shapes the summary takes - the layout, the
 * counts and the marks belong to the summary, so every adapter's rail matches.
 */
export const ChangeRail: React.FC<Props> = ({
  configuration, groups, activeSection, labelFor, onJump
}) => {
  const { t } = useTranslation()

  const sections: ConfigSummarySection[] = groups.map((group) => ({
    key: group.section,
    label: t(group.label),
    rows: group.changes.map((change) => ({
      key: change.address,
      label: labelFor(change),
      hint: change.address,
      status: change.status
    }))
  }))

  return (
    <ConfigSummary
      activeKey={ activeSection }
      name={ typeof configuration.general?.name === 'string' ? configuration.general.name : '' }
      onOpenSection={ onJump }
      sections={ sections }
    />
  )
}
