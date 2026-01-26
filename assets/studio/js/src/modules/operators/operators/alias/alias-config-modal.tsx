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
import { Form, Input } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type OperatorConfigModalProps } from '../../dynamic-type-operator-abstract'
import { type AliasAttributes } from './dynamic-type-operator-alias'
import { OperatorModal } from '../../components/operator-modal'

export const AliasConfigModal = (props: OperatorConfigModalProps<AliasAttributes>): React.JSX.Element => {
  const { t } = useTranslation()

  return (
    <OperatorModal { ...props }>
      {() => (
        <Form.Item
          label={ t('data-hub.label') }
          name="label"
        >
          <Input />
        </Form.Item>
      )}
    </OperatorModal>
  )
}
