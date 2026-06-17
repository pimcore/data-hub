/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useEffect } from 'react'
import { IconTextButton } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type DataHubAdapterDetailViewProps } from '../../config/dynamic-types/dynamic-type-data-hub-adapter-abstract'
import { BaseDetailView, type TabItem, ConfigToolbar, useDetailView, trackDataHubError } from '../../../components/base-detail-view'
import { GeneralTab } from './tabs/general-tab'
import { SchemaDefinitionTab } from './tabs/schema-definition-tab'
import { SecurityDefinitionTab } from './tabs/security-definition-tab'
import { PermissionsTab } from './tabs/permissions-tab'
import { useBundleDataHubGraphqlExplorerUrlQuery } from '../graphql-api-slice-enhanced'
import { useBundleDataHubConfigGetQuery, useBundleDataHubConfigUpdateMutation } from '../../config/config-api-slice-enhanced'
import { isEmpty, isNil } from 'lodash'
import { type GraphQLFormValues } from './types'
import { transformFormToBackend, transformBackendToForm } from '../utils/transformers'
import { type BackendConfiguration } from './backend-types'

export const GraphQLDetailView = ({ configName, onChange, onDelete }: DataHubAdapterDetailViewProps): React.JSX.Element => {
  const { t } = useTranslation()

  // API hooks
  const { data: configData, error: fetchError, isLoading, isFetching, refetch, requestId } = useBundleDataHubConfigGetQuery(
    { name: configName },
    { refetchOnMountOrArgChange: true }
  )
  const { data: explorerUrlData } = useBundleDataHubGraphqlExplorerUrlQuery({ name: configName })
  const [updateConfig, { isLoading: isSaving }] = useBundleDataHubConfigUpdateMutation()

  // Error tracking. Save errors are surfaced centrally by useDetailView (via .unwrap()); only the
  // fetch error needs to be reported here.
  useEffect(() => {
    if (!isNil(fetchError)) {
      trackDataHubError(fetchError)
    }
  }, [fetchError])

  const loading = isLoading || isFetching
  const backendConfig = (configData?.configuration ?? {}) as BackendConfiguration
  // Editable only when the user holds the update permission AND the config is not location-locked.
  const userPermissions = (configData?.userPermissions ?? {}) as { update?: boolean, delete?: boolean }
  const storeWriteable = backendConfig?.general?.writeable !== false
  const isWriteable = userPermissions.update === true && storeWriteable
  // Delete permission controls whether the delete button is shown; the writeable state controls
  // whether it is enabled (handled by the toolbar via isWriteable).
  const canDelete = userPermissions.delete === true

  const handleSaveToApi = async (updatedConfig: BackendConfiguration, modificationDate: number): Promise<{ modificationDate?: number }> => {
    const response = await updateConfig({
      name: configName,
      bundleDataHubUpdateConfiguration: {
        data: JSON.stringify(updatedConfig),
        modificationDate
      }
    }).unwrap()

    return { modificationDate: response?.modificationDate }
  }

  // Shared form state management
  const { form, isDirty, initialValues, handleSave, handleValuesChange } = useDetailView<GraphQLFormValues, BackendConfiguration>({
    configName,
    configData: backendConfig,
    modificationDate: configData?.modificationDate,
    isLoading: loading,
    requestId,
    isWriteable,
    transformToForm: transformBackendToForm,
    transformToBackend: transformFormToBackend,
    onSave: handleSaveToApi,
    onChange
  })

  const handleOpenInTab = (): void => {
    if (explorerUrlData !== undefined && !isEmpty(explorerUrlData.explorerUrl)) {
      let explorerUrl = explorerUrlData.explorerUrl
      const securityMethod = form.getFieldValue(['security', 'method']) as string | undefined
      if (securityMethod === 'datahub_apikey') {
        const apikey = form.getFieldValue(['security', 'apikey']) as string | undefined
        if (!isNil(apikey) && !isEmpty(apikey)) {
          const firstKey = apikey.split('\n')[0]
          if (!isEmpty(firstKey)) {
            explorerUrl = `${explorerUrl}?apikey=${firstKey}`
          }
        }
      }
      window.open(explorerUrl, '_blank')
    }
  }

  const tabs: TabItem[] = [
    {
      key: 'general',
      label: t('data-hub.tabs.general'),
      children: <GeneralTab adapterTypeLabel={ t('data-hub.adapter.graphql') } />
    },
    {
      key: 'schema',
      label: t('data-hub.tabs.schema-definition'),
      children: <SchemaDefinitionTab isWriteable={ isWriteable } />
    },
    {
      key: 'security',
      label: t('data-hub.tabs.security-definition'),
      children: <SecurityDefinitionTab isWriteable={ isWriteable } />
    },
    {
      key: 'permissions',
      label: t('data-hub.tabs.permissions'),
      children: <PermissionsTab isWriteable={ isWriteable } />
    }
  ]

  const toolbar = (
    <ConfigToolbar
      additionalButtons={ [
        <IconTextButton
          disabled={ false }
          icon={ { value: 'graphql', colorToken: 'colorCodingViolet4' } }
          key="open-in-tab"
          onClick={ handleOpenInTab }
        >
          {t('data-hub.open-in-tab')}
        </IconTextButton>
      ] }
      canDelete={ canDelete }
      configName={ configName }
      isDirty={ isDirty }
      isLoading={ loading }
      isSaving={ isSaving }
      isWriteable={ isWriteable }
      onDelete={ onDelete }
      onRefresh={ refetch }
      onSave={ handleSave }
    />
  )

  return (
    <BaseDetailView
      disabled={ !isWriteable }
      form={ form }
      initialValues={ initialValues }
      isLoading={ loading }
      onValuesChange={ handleValuesChange }
      requestId={ requestId ?? '' }
      tabs={ tabs }
      toolbar={ toolbar }
    />
  )
}
