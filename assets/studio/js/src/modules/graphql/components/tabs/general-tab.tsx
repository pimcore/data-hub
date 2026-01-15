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
import { Form, Input, Switch, TextArea, Flex, Text, FormKit } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'

export const GeneralTab = (): React.JSX.Element => {
  const { t } = useTranslation()

  return (
    <FormKit.Panel contentPadding="extra-small">
      <Form.Item
        name="active"
        valuePropName="checked"
      >
        <Flex
          align="center"
          gap="small"
        >
          <Switch />
          <Text>{ t('data-hub.config.active') }</Text>
        </Flex>
      </Form.Item>

      <Form.Item
        label={ t('data-hub.config.type') }
        name="type"
      >
        <Input disabled />
      </Form.Item>

      <Form.Item
        label={ t('data-hub.config.name') }
        name="name"
      >
        <Input disabled />
      </Form.Item>

      <Form.Item
        label={ t('data-hub.config.description') }
        name="description"
      >
        <TextArea rows={ 4 } />
      </Form.Item>

      <Form.Item
        label={ t('data-hub.config.group') }
        name="group"
      >
        <Input />
      </Form.Item>
    </FormKit.Panel>
  )
}
