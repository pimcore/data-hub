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
import { Form, Select, TextArea, Switch, IconButton, Flex, FormKit, Tooltip } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { isNil } from 'lodash'
import { WorkspaceGrid } from './security-definition-tab/workspace-grid'

interface SecurityDefinitionTabProps {
  onFormChange?: () => void
}

export const SecurityDefinitionTab = ({ onFormChange }: SecurityDefinitionTabProps): React.JSX.Element => {
  const { t } = useTranslation()
  const form = Form.useFormInstance()

  const generateApiKey = (): void => {
    const currentValue = form.getFieldValue(['security', 'apikey']) as string | undefined
    const currentValueStr = !isNil(currentValue) ? currentValue : ''
    const newKey = generateRandomKey()
    const newValue = currentValueStr.length > 0 ? `${currentValueStr}\n${newKey}` : newKey

    form.setFieldsValue({
      security: {
        apikey: newValue
      }
    })

    // Trigger form change to enable save button
    if (!isNil(onFormChange)) {
      onFormChange()
    }
  }

  const generateRandomKey = (): string => {
    return Array.from(crypto.getRandomValues(new Uint8Array(16)))
      .map(b => b.toString(16).padStart(2, '0'))
      .join('')
  }

  const methodOptions = [
    {
      value: 'datahub_apikey',
      label: t('data-hub.security.method.apikey')
    }
  ]

  return (
    <FormKit.Panel contentPadding="extra-small">
      <FormKit.Panel
        contentPadding="extra-small"
        title={ t('data-hub.security.authentication') }
      >
        <Form.Item
          initialValue="datahub_apikey"
          label={ t('data-hub.security.method') }
          name={ ['security', 'method'] }
        >
          <Select
            options={ methodOptions }
          />
        </Form.Item>

        <Form.Item
          help={ t('data-hub.security.apikey-description') }
          label={ t('data-hub.security.apikey') }
        >
          <Flex
            align="flex-start"
            gap={ 8 }
          >
            <Form.Item
              name={ ['security', 'apikey'] }
              noStyle
            >
              <TextArea
                autoSize={ { minRows: 4, maxRows: 10 } }
              />
            </Form.Item>
            <Tooltip title={ t('data-hub.security.generate-apikey') }>
              <IconButton
                icon={ { value: 'asset' } }
                onClick={ generateApiKey }
                type="default"
              />
            </Tooltip>
          </Flex>
        </Form.Item>

        <Form.Item
          name={ ['security', 'skipPermissionCheck'] }
          valuePropName="checked"
        >
          <Switch labelRight={ t('data-hub.security.skip-permission-check') } />
        </Form.Item>

        <Form.Item
          help={ t('data-hub.security.introspection-description') }
          name={ ['security', 'disableIntrospection'] }
          valuePropName="checked"
        >
          <Switch labelRight={ t('data-hub.security.disable-introspection') } />
        </Form.Item>
      </FormKit.Panel>

      <FormKit.Panel
        contentPadding="extra-small"
        title={ t('data-hub.workspaces.title') }
      >
        <Flex
          gap="small"
          vertical
        >
          <Form.Item
            name={ ['workspaces', 'documents'] }
            noStyle
          >
            <WorkspaceGrid type="documents" />
          </Form.Item>

          <Form.Item
            name={ ['workspaces', 'assets'] }
            noStyle
          >
            <WorkspaceGrid type="assets" />
          </Form.Item>

          <Form.Item
            name={ ['workspaces', 'objects'] }
            noStyle
          >
            <WorkspaceGrid type="objects" />
          </Form.Item>
        </Flex>
      </FormKit.Panel>
    </FormKit.Panel>
  )
}
