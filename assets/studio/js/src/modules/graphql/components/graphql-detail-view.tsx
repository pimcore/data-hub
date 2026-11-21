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
import { ConfigToolbar, trackConfigError } from '../../../components/base-detail-view'
import { useBundleDataHubGraphqlExplorerUrlQuery } from '../graphql-api-slice-enhanced'
import { useBundleDataHubConfigGetQuery, useBundleDataHubConfigUpdateMutation } from '../../config/config-api-slice-enhanced'
import { isEmpty, isNil } from 'lodash'
import { type BackendConfiguration } from './backend-types'
import { GraphQLConfigEditor, type GraphQLEditorToolbarState } from './graphql-config-editor'

export const GraphQLDetailView = ({ configName, onChange, onDelete }: DataHubAdapterDetailViewProps): React.JSX.Element => {
  const { t } = useTranslation()

  // API hooks
  const { data: configData, error: fetchError, isLoading, isFetching, refetch, requestId } = useBundleDataHubConfigGetQuery(
    { name: configName },
    { refetchOnMountOrArgChange: true }
  )
  const { data: explorerUrlData } = useBundleDataHubGraphqlExplorerUrlQuery({ name: configName })
  const [updateConfig, { isLoading: isSaving }] = useBundleDataHubConfigUpdateMutation()

  // Save errors are surfaced centrally by useDetailView; only the fetch error is reported here.
  useEffect(() => {
    if (!isNil(fetchError)) {
      trackConfigError(fetchError)
    }
  }, [fetchError])

  const loading = isLoading || isFetching
  const backendConfig = (configData?.configuration ?? {}) as BackendConfiguration
  const userPermissions = (configData?.userPermissions ?? {}) as { update?: boolean, delete?: boolean }
  const storeWriteable = backendConfig?.general?.writeable !== false
  const isWriteable = userPermissions.update === true && storeWriteable
  const canDelete = userPermissions.delete === true && storeWriteable
  const saveDisabledTooltipKey = storeWriteable && userPermissions.update !== true ? 'data-hub.config.no-update-permission' : 'config_not_writeable'

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

  const handleOpenInTab = (fieldValue: GraphQLEditorToolbarState['fieldValue']): void => {
    if (explorerUrlData !== undefined && !isEmpty(explorerUrlData.explorerUrl)) {
      let explorerUrl = explorerUrlData.explorerUrl
      const securityMethod = fieldValue(['security', 'method']) as string | undefined
      if (securityMethod === 'datahub_apikey') {
        const apikey = fieldValue(['security', 'apikey']) as string | undefined
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

  const renderToolbar = ({ isDirty, onSave, fieldValue }: GraphQLEditorToolbarState): React.ReactNode => (
    <ConfigToolbar
      additionalButtons={ [
        <IconTextButton
          disabled={ false }
          icon={ { value: 'graphql', colorToken: 'colorCodingViolet4' } }
          key="open-in-tab"
          onClick={ () => { handleOpenInTab(fieldValue) } }
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
      onSave={ onSave }
      saveDisabledTooltipKey={ saveDisabledTooltipKey }
    />
  )

  return (
    <GraphQLConfigEditor
      configName={ configName }
      configuration={ backendConfig }
      isLoading={ loading }
      isWriteable={ isWriteable }
      modificationDate={ configData?.modificationDate }
      onChange={ onChange }
      onSave={ handleSaveToApi }
      renderToolbar={ renderToolbar }
      requestId={ requestId ?? '' }
    />
  )
}
