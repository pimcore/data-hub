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
import { Content, ContentLayout, FormKit, Tabs } from '@pimcore/studio-ui-bundle/components'
import { type DataHubAdapterDetailViewProps } from '../../config/dynamic-types/dynamic-type-data-hub-adapter-abstract'
import { type TabItem, useDetailView } from '../../../components/base-detail-view'
import { useStyles } from '../../../components/base-detail-view/base-detail-view.styles'
import { GeneralTab } from './tabs/general-tab'
import { SchemaDefinitionTab } from './tabs/schema-definition-tab'
import { SecurityDefinitionTab } from './tabs/security-definition-tab'
import { PermissionsTab } from './tabs/permissions-tab'
import { type GraphQLFormValues } from './types'
import { transformFormToBackend, transformBackendToForm } from '../utils/transformers'
import { type BackendConfiguration } from './backend-types'

/** what a toolbar can only know from inside the form */
export interface GraphQLEditorToolbarState {
  isDirty: boolean
  onSave: () => void
  /** a field as the form currently holds it */
  fieldValue: (name: string[]) => unknown
}

export interface GraphQLConfigEditorProps {
  configName: string
  configuration: BackendConfiguration
  isWriteable: boolean
  onSave: (configuration: BackendConfiguration, modificationDate: number) => Promise<{ modificationDate?: number }>
  isLoading?: boolean
  modificationDate?: number
  onChange?: DataHubAdapterDetailViewProps['onChange']
  requestId?: string
  renderToolbar?: (state: GraphQLEditorToolbarState) => React.ReactNode
  /** drive the tab from outside; omit to keep the tab strip's own state */
  activeTab?: string
  onTabChange?: (key: string) => void
}

/** the tab keys, so a caller can address one without repeating the strings */
export const GRAPHQL_TABS = {
  general: 'general',
  schema: 'schema',
  security: 'security',
  permissions: 'permissions'
} as const

/** a mount that does not report dirty state still has to satisfy useDetailView */
const ignoreChange = (): void => undefined

/**
 * The GraphQL editor, over a configuration it is handed rather than one it fetches, so the tab
 * tree renders against any source of a configuration document - the detail API or a change set.
 */
export const GraphQLConfigEditor = ({
  configName,
  configuration,
  isWriteable,
  onSave,
  isLoading = false,
  modificationDate,
  onChange = ignoreChange,
  requestId,
  renderToolbar,
  activeTab,
  onTabChange
}: GraphQLConfigEditorProps): React.JSX.Element => {
  const { t } = useTranslation()
  const { styles } = useStyles()

  const { form, isDirty, initialValues, handleSave, handleValuesChange } = useDetailView<GraphQLFormValues, BackendConfiguration>({
    configName,
    configData: configuration,
    modificationDate,
    isLoading,
    requestId,
    isWriteable,
    transformToForm: transformBackendToForm,
    transformToBackend: transformFormToBackend,
    onSave,
    onChange
  })

  const tabs: TabItem[] = [
    {
      key: GRAPHQL_TABS.general,
      label: t('data-hub.tabs.general'),
      children: <GeneralTab adapterTypeLabel={ t('data-hub.adapter.graphql') } />
    },
    {
      key: GRAPHQL_TABS.schema,
      label: t('data-hub.tabs.schema-definition'),
      children: <SchemaDefinitionTab isWriteable={ isWriteable } />
    },
    {
      key: GRAPHQL_TABS.security,
      label: t('data-hub.tabs.security-definition'),
      children: <SecurityDefinitionTab isWriteable={ isWriteable } />
    },
    {
      key: GRAPHQL_TABS.permissions,
      label: t('data-hub.tabs.permissions'),
      children: <PermissionsTab isWriteable={ isWriteable } />
    }
  ]

  // BaseDetailView owns its tab state (defaultActiveKey, no onChange), so the shell is
  // inlined here instead - the review rail has to be able to open the tab a change sits in.
  const panes = tabs.map((tab) => ({
    ...tab,
    children: tab.fullHeight === true
      ? tab.children
      : <Content padded>{ tab.children }</Content>
  }))

  const fieldValue = (name: string[]): unknown => form.getFieldValue(name)

  return (
    <ContentLayout renderToolbar={ renderToolbar?.({ isDirty, onSave: handleSave, fieldValue }) }>
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
                onValuesChange: handleValuesChange,
                disabled: !isWriteable
              } }
              key={ requestId }
              wrapInPanel={ false }
            >
              <Tabs
                activeKey={ activeTab }
                defaultActiveKey={ GRAPHQL_TABS.general }
                fullHeight
                items={ panes }
                noTabBarMargin
                onChange={ onTabChange }
                type="card"
              />
            </FormKit>
          </div>
        )}
      </Content>
    </ContentLayout>
  )
}
