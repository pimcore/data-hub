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
import { Form, Select, TextArea, Switch, Button, Flex, FormKit, Box } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { WorkspaceGrid } from './security-definition-tab/workspace-grid'

export const SecurityDefinitionTab = (): React.JSX.Element => {
  const { t } = useTranslation()
  const form = Form.useFormInstance()

  const generateApiKey = (): void => {
    const currentValue = form.getFieldValue(['security', 'apikey']) as string | undefined
    const currentValueStr = String(currentValue ?? '')
    const newKey = generateRandomKey()
    const newValue = currentValueStr.length > 0 ? `${currentValueStr}\n${newKey}` : newKey

    form.setFieldsValue({
      security: {
        apikey: newValue
      }
    }, { triggerChange: true })
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
          label={ t('data-hub.security.apikey') }
          name={ ['security', 'apikey'] }
          tooltip={ t('data-hub.security.apikey-description') }
        >
          <TextArea
            autoSize={ { minRows: 4, maxRows: 10 } }
          />
        </Form.Item>

        <Box margin={ { bottom: 'small' } }>
          <Button
            onClick={ generateApiKey }
            type="default"
          >
            {t('data-hub.security.generate-apikey')}
          </Button>
        </Box>

        <Form.Item
          name={ ['security', 'skipPermissionCheck'] }
          valuePropName="checked"
        >
          <Switch labelRight={ t('data-hub.security.skip-permission-check') } />
        </Form.Item>

        <Form.Item
          name={ ['security', 'disableIntrospection'] }
          valuePropName="checked"
        >
          <Switch
            labelRight={ t('data-hub.security.disable-introspection') }
            tooltip={ t('data-hub.security.introspection-description') }
          />
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
