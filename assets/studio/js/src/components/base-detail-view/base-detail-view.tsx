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

export interface TabItem {
  key: string
  label: string
  children: ReactNode
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
  return (
    <ContentLayout renderToolbar={ toolbar }>
      <Content
        loading={ isLoading }
        padded
      >
        {!isLoading && (
          <FormKit
            formProps={ {
              form,
              initialValues,
              layout: 'vertical',
              onValuesChange,
              disabled
            } }
            key={ requestId }
          >
            <Tabs
              defaultActiveKey="general"
              items={ tabs }
              type="card"
            />
          </FormKit>
        )}
      </Content>
    </ContentLayout>
  )
}
