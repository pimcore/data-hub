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
import { QueryGrid } from './schema-definition-tab/query-grid'
import { MutationGrid } from './schema-definition-tab/mutation-grid'
import { GenericTypesGrid } from './schema-definition-tab/generic-types-grid'
import { FieldWidthContainer } from '../../../config/components/field-width-container'

interface SchemaDefinitionTabProps {
  isWriteable?: boolean
}

export const SchemaDefinitionTab = ({ isWriteable = true }: SchemaDefinitionTabProps): React.JSX.Element => {
  return (
    <FormKit.Panel contentPadding="extra-small">
      <FieldWidthContainer>
        <Flex
          gap="small"
          vertical
        >
          <Form.Item
            name={ ['schema', 'query'] }
            noStyle
          >
            <QueryGrid isWriteable={ isWriteable } />
          </Form.Item>

          <Form.Item
            name={ ['schema', 'mutation'] }
            noStyle
          >
            <MutationGrid isWriteable={ isWriteable } />
          </Form.Item>

          <Form.Item
            name={ ['schema', 'genericTypes'] }
            noStyle
          >
            <GenericTypesGrid isWriteable={ isWriteable } />
          </Form.Item>
        </Flex>
      </FieldWidthContainer>
    </FormKit.Panel>
  )
}
