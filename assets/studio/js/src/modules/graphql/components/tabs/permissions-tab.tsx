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
import { Form, Flex, FormKit } from '@pimcore/studio-ui-bundle/components'
import { PermissionGrid } from './permissions-tab/permission-grid'
import { FieldWidthContainer } from '../../../config/components/field-width-container'

export const PermissionsTab = (): React.JSX.Element => {
  return (
    <FormKit.Panel contentPadding="extra-small">
      <FieldWidthContainer>
        <Flex
          gap="small"
          vertical
        >
          <Form.Item
            name={ ['permissions', 'roles'] }
            noStyle
          >
            <PermissionGrid
              type="roles"
            />
          </Form.Item>
          <Form.Item
            name={ ['permissions', 'users'] }
            noStyle
          >
            <PermissionGrid
              type="users"
            />
          </Form.Item>
        </Flex>
      </FieldWidthContainer>
    </FormKit.Panel>
  )
}
