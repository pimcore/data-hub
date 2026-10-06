/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useEffect, useMemo, useState } from 'react'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { Content } from '@pimcore/studio-ui-bundle/components'
import { ReviewOutcomeHeader, type ReviewDiffViewProps } from '@pimcore/change-control-bundle/sdk'
import { GraphQLConfigEditor, GRAPHQL_TABS } from '../components/graphql-config-editor'
import { useBundleDataHubConfigGetQuery } from '../../config/config-api-slice-enhanced'
import { type BackendConfiguration } from '../components/backend-types'
import { FormAnnotationsProvider } from './studio-form-annotations'
import {
  annotationsFor, configChanges, groupChanges, isNewConfiguration, proposedConfiguration,
  SECTION_TARGET, type ConfigChange
} from './config-review-model'
import { fieldLabel } from './field-labels'
import { ChangeRail } from './change-rail'
import { configBrief } from './config-brief'
import { ConfigBriefCard } from './config-brief-card'
import { useStyles } from './graphql-config-review-surface.styles'
import { useChangeSetReview } from './use-change-set-review'

/** a no-op save: the editor is mounted read-only and nothing here can write */
const noSave = async (): Promise<{ modificationDate?: number }> => ({})

/**
 * A GraphQL endpoint reviews as the bundle's own editor: the proposed configuration mounted
 * read-only, with every changed field marked where it sits and a rail that opens the tab a
 * change lives in.
 */
export const GraphQLConfigReviewSurface: React.FC<ReviewDiffViewProps> = ({
  subjectRef, changeSetId, contextRef, compareAgainst = 'current', onStatsChange,
  readOnly, state, resolvedAt
}) => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const { data: payload, isLoading, error } = useChangeSetReview(changeSetId, contextRef)

  const isNew = useMemo(() => isNewConfiguration(payload, compareAgainst), [payload, compareAgainst])

  // the review payload carries only what changed; the editor needs the whole document — and
  // the paths a proposal may never carry have no other source at all
  // — a configuration the proposal creates has no live side yet
  const { data: liveConfig, isLoading: liveLoading } = useBundleDataHubConfigGetQuery({ name: subjectRef }, { skip: payload === undefined || isNew })
  const live = liveConfig?.configuration as BackendConfiguration | undefined

  const changes = useMemo(() => configChanges(payload, compareAgainst), [payload, compareAgainst])
  const configuration = useMemo(() => proposedConfiguration(payload, live), [payload, live])
  const groups = useMemo(() => groupChanges(changes), [changes])
  const brief = useMemo(() => isNew ? configBrief(configuration, t) : undefined, [isNew, configuration, t])
  // a new configuration is reviewed whole; marking every field says only "all of it"
  const annotations = useMemo(() => isNew ? {} : annotationsFor(changes), [isNew, changes])

  const [activeTab, setActiveTab] = useState<string>(GRAPHQL_TABS.general)

  // open the tab the first change sits in, so the reader lands on it rather than on General
  useEffect(() => {
    const first = groups[0]?.section
    const target = first === undefined ? undefined : SECTION_TARGET[first]?.tab
    if (target !== undefined) setActiveTab(target)
  }, [groups])

  useEffect(() => { onStatsChange?.(changes.length) }, [changes.length, onStatsChange])

  const labelFor = (change: ConfigChange): string => fieldLabel(change.address, t)

  const activeSection = useMemo(
    () => Object.keys(SECTION_TARGET).find((section) => SECTION_TARGET[section].tab === activeTab),
    [activeTab]
  )

  const jump = (section: string): void => {
    const target = SECTION_TARGET[section]?.tab
    if (target !== undefined) setActiveTab(target)
  }

  const editor = (
    <GraphQLConfigEditor
      activeTab={ activeTab }
      configName={ subjectRef }
      configuration={ configuration }
      isWriteable={ false }
      onSave={ noSave }
      onTabChange={ setActiveTab }
      requestId={ changeSetId }
    />
  )

  if (error !== undefined && payload === undefined) {
    return <div className={ styles.empty }>{ t('data-hub.review.unavailable') }</div>
  }

  return (
    <Content loading={ isLoading || liveLoading }>
      { readOnly === true && state != null && (
        <ReviewOutcomeHeader
          flush
          resolvedAt={ resolvedAt ?? 0 }
          state={ state }
        />
      ) }
      <div className={ styles.split }>
        <div className={ styles.rail }>
          { brief !== undefined
            ? (
              <ConfigBriefCard
                activeSection={ activeSection }
                brief={ brief }
                onJump={ jump }
              />
              )
            : (
              <ChangeRail
                activeSection={ activeSection }
                configuration={ configuration }
                groups={ groups }
                labelFor={ labelFor }
                onJump={ jump }
              />
              ) }
        </div>

        <div className={ styles.editor }>
          { FormAnnotationsProvider !== null
            ? <FormAnnotationsProvider annotations={ annotations }>{ editor }</FormAnnotationsProvider>
            : editor }
        </div>
      </div>
    </Content>
  )
}
