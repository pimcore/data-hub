/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { type ReactNode } from 'react'
import { Tabs, FormKit, Content, ContentLayout, type formInstanceType } from '@pimcore/studio-ui-bundle/components'
import { useStyles } from './base-detail-view.styles'

export interface TabItem {
  key: string
  label: string
  children: ReactNode
  /** When true, the tab content fills full height without a scrollable Panel wrapper. */
  fullHeight?: boolean
}

export interface BaseDetailViewProps {
  toolbar: ReactNode
  tabs: TabItem[]
  isLoading: boolean
  form: formInstanceType
  initialValues: any
  onValuesChange: (changedValues: any, allValues: any) => void
  disabled?: boolean
  requestId?: string
}

export function BaseDetailView ({
  toolbar,
  tabs,
  isLoading,
  form,
  initialValues,
  onValuesChange,
  disabled = false,
  requestId
}: BaseDetailViewProps): React.JSX.Element {
  const { styles } = useStyles()

  const enhancedTabs = tabs.map((tab) => ({
    ...tab,
    children: tab.fullHeight === true
      ? tab.children
      : (
        <Content padded>
          { tab.children }
        </Content>
        )
  }))

  return (
    <ContentLayout renderToolbar={ toolbar }>
      <Content
        loading={ isLoading }
        overflow={ { x: 'auto', y: 'hidden' } }
      >
        {!isLoading && (
          <div className={ styles.formWrapper }>
            <FormKit
              formProps={ {
                form,
                initialValues,
                layout: 'vertical',
                onValuesChange,
                disabled
              } }
              key={ requestId }
              wrapInPanel={ false }
            >
              <Tabs
                defaultActiveKey="general"
                fullHeight
                items={ enhancedTabs }
                noTabBarMargin
                type="card"
              />
            </FormKit>
          </div>
        )}
      </Content>
    </ContentLayout>
  )
}
