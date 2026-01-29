/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useEffect, useState, useMemo, useRef } from 'react'
import { Form, Tabs, Button, FormKit, Portal, IconTextButton, ButtonGroup, useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type AdapterFormProps } from '../../config/dynamic-types/dynamic-type-data-hub-adapter-abstract'
import { GeneralTab } from './tabs/general-tab'
import { SchemaDefinitionTab } from './tabs/schema-definition-tab'
import { SecurityDefinitionTab } from './tabs/security-definition-tab'
import { PermissionsTab } from './tabs/permissions-tab'
import { useBundleDataHubGraphqlExplorerUrlQuery } from '../graphql-api-slice-enhanced'
import { useBundleDataHubConfigUpdateMutation } from '../../config/config-api-slice-enhanced'
import { ApiError, trackError } from '@pimcore/studio-ui-bundle/modules/app'
import { isNil, isEmpty } from 'lodash'
import { type GraphQLFormValues } from './types'
import { transformFormToBackend, transformBackendToForm } from '../utils/transformers'
import { type BackendConfiguration } from './backend-types'

export const GraphQLAdapterForm = ({ config, configName, configId, onChange, isActive }: AdapterFormProps): React.JSX.Element => {
  const [form] = Form.useForm()
  const { t } = useTranslation()
  const messageApi = useMessage()
  const [isDirty, setIsDirty] = useState(false)
  const modificationDateRef = useRef<number>(config.modificationDate ?? Math.floor(Date.now() / 1000))

  const { data: explorerUrlData } = useBundleDataHubGraphqlExplorerUrlQuery({ name: configName })
  const [updateConfig, { error: updateError, isLoading: isSaving }] = useBundleDataHubConfigUpdateMutation()
  const portalId = 'data-hub-save-button'

  useEffect(() => {
    if (!isNil(updateError)) {
      trackError(new ApiError(updateError))
    }
  }, [updateError])

  const configData = config.configuration as BackendConfiguration

  const initialValues = useMemo<GraphQLFormValues>(() =>
    transformBackendToForm(configData, configName),
  [configData, configName])

  useEffect(() => {
    form.setFieldsValue(initialValues)
    setIsDirty(false)
    onChange(false)
    modificationDateRef.current = config.modificationDate ?? Math.floor(Date.now() / 1000)
  }, [initialValues, form, onChange, config.modificationDate])

  const onValuesChange = (): void => {
    setIsDirty(true)
    onChange(true)
  }

  const handleFormChange = (): void => {
    setIsDirty(true)
    onChange(true)
  }

  const handleSave = (): void => {
    form.validateFields().then(async (values) => {
      try {
        // Merge with initial values to preserve data from unrendered tabs
        const mergedValues: GraphQLFormValues = {
          ...initialValues,
          ...values as GraphQLFormValues
        }

        const updatedConfig = transformFormToBackend(mergedValues, configData)

        const { data: response } = await updateConfig({
          name: configName,
          bundleDataHubUpdateConfiguration: {
            data: JSON.stringify(updatedConfig),
            modificationDate: modificationDateRef.current
          }
        })

        if (!isNil(response?.modificationDate)) {
          modificationDateRef.current = response.modificationDate
        }

        setIsDirty(false)
        onChange(false)

        void messageApi.success(t('save-success'))
      } catch (error) {
        console.error('Failed to save configuration:', error)
      }
    }).catch((error) => {
      console.error('Validation failed:', error)
    })
  }

  const handleOpenInTab = (): void => {
    if (explorerUrlData !== undefined && !isEmpty(explorerUrlData.explorerUrl)) {
      window.open(explorerUrlData.explorerUrl, '_blank')
    }
  }

  const renderSaveButton = (): React.JSX.Element | null => {
    if (!isActive) {
      return null
    }

    return (
      <Portal targetId={ portalId }>
        <ButtonGroup
          items={ [
            <IconTextButton
              icon={ { value: 'graphql', colorToken: 'colorCodingViolet4' } }
              key="open-in-tab"
              onClick={ handleOpenInTab }
            >
              {t('data-hub.open-in-tab')}
            </IconTextButton>,
            <Button
              disabled={ !isDirty }
              key="save"
              loading={ isSaving }
              onClick={ handleSave }
              type="primary"
            >
              {t('save')}
            </Button>
          ] }
        />
      </Portal>
    )
  }

  const tabItems = [
    {
      key: 'general',
      label: t('data-hub.tabs.general'),
      children: <GeneralTab />
    },
    {
      key: 'schema',
      label: t('data-hub.tabs.schema-definition'),
      children: <SchemaDefinitionTab onFormChange={ handleFormChange } />
    },
    {
      key: 'security',
      label: t('data-hub.tabs.security-definition'),
      children: <SecurityDefinitionTab onFormChange={ handleFormChange } />
    },
    {
      key: 'permissions',
      label: t('data-hub.tabs.permissions'),
      children: <PermissionsTab />
    }
  ]

  return (
    <FormKit
      formProps={ {
        form,
        initialValues,
        layout: 'vertical',
        onValuesChange
      } }
    >
      <Tabs
        defaultActiveKey="general"
        items={ tabItems }
        type="card"
      />
      {renderSaveButton()}
    </FormKit>
  )
}
