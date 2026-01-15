/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useEffect, useState, useMemo } from 'react'
import { Form, Tabs, Button, FormKit, Portal, IconTextButton, ButtonGroup } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type AdapterFormProps } from '../../config/dynamic-types/dynamic-type-data-hub-adapter-abstract'
import { GeneralTab } from './tabs/general-tab'
import { SecurityDefinitionTab } from './tabs/security-definition-tab'
import { useBundleDataHubGraphqlExplorerUrlQuery } from '../graphql-api-slice-enhanced'

export const GraphQLAdapterForm = ({ config, configName, configId, onChange }: AdapterFormProps): React.JSX.Element => {
  const [form] = Form.useForm()
  const { t } = useTranslation()
  const [isDirty, setIsDirty] = useState(false)
  const [isSaving, setIsSaving] = useState(false)

  const { data: explorerUrlData } = useBundleDataHubGraphqlExplorerUrlQuery({ name: configName })
  const portalId = `data-hub-save-button-${configId}`

  const configData = config.configuration as any

  const initialValues = useMemo(() => ({
    active: configData?.general?.active ?? true,
    type: 'GraphQL',
    name: configName,
    description: configData?.general?.description ?? '',
    group: configData?.general?.group ?? '',
    security: {
      method: configData?.security?.method ?? 'datahub_apikey',
      apikey: configData?.security?.apikey ? (Array.isArray(configData.security.apikey) ? configData.security.apikey.join('\n') : configData.security.apikey) : '',
      skipPermissionCheck: configData?.security?.skipPermissionCheck ?? false,
      disableIntrospection: configData?.security?.disableIntrospection ?? false
    },
    workspaces: {
      documents: (configData?.workspaces?.document || []).map((ws: any) => ({
        path: ws.cpath || '',
        create: ws.create || false,
        read: ws.read || false,
        update: ws.update || false,
        delete: ws.delete || false
      })),
      assets: (configData?.workspaces?.asset || []).map((ws: any) => ({
        path: ws.cpath || '',
        create: ws.create || false,
        read: ws.read || false,
        update: ws.update || false,
        delete: ws.delete || false
      })),
      objects: (configData?.workspaces?.object || []).map((ws: any) => ({
        path: ws.cpath || '',
        create: ws.create || false,
        read: ws.read || false,
        update: ws.update || false,
        delete: ws.delete || false
      }))
    }
  }), [configData, configName])

  useEffect(() => {
    form.setFieldsValue(initialValues)
    setIsDirty(false)
    onChange(false)
  }, [initialValues, form, onChange])

  const onValuesChange = (): void => {
    setIsDirty(true)
    onChange(true)
  }

  const handleSave = (): void => {
    form.validateFields().then((values) => {
      setIsSaving(true)
      
      // TODO: Implement update mutation when backend endpoint is available
      console.log('Saving configuration:', values)
      
      // Simulate save for now
      setTimeout(() => {
        setIsSaving(false)
        setIsDirty(false)
      }, 500)
    }).catch((error) => {
      console.error('Validation failed:', error)
    })
  }

  const handleOpenInTab = (): void => {
    if (explorerUrlData?.explorerUrl) {
      window.open(explorerUrlData.explorerUrl, '_blank')
    }
  }

  const renderSaveButton = (): React.JSX.Element => (
    <Portal targetId={ portalId }>
      <ButtonGroup
        items={ [
          <IconTextButton
            key="open-in-tab"
            icon={ { value: 'new-tab' } }
            onClick={ handleOpenInTab }
          >
            {t('data-hub.open-in-tab')}
          </IconTextButton>,
          <Button
            key="save"
            disabled={ !isDirty }
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

  const tabItems = [
    {
      key: 'general',
      label: t('data-hub.tabs.general'),
      children: <GeneralTab />
    },
    {
      key: 'schema',
      label: t('data-hub.tabs.schema-definition'),
      children: <div>Schema Definition - Coming soon</div>
    },
    {
      key: 'security',
      label: t('data-hub.tabs.security-definition'),
      children: <SecurityDefinitionTab onFormChange={ onValuesChange } />
    },
    {
      key: 'permissions',
      label: t('data-hub.tabs.permissions'),
      children: <div>Permissions - Coming soon</div>
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
