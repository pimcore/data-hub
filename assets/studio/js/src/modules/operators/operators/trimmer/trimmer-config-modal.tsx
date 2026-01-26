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
import { Form, Input, Select } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type OperatorConfigModalProps } from '../../dynamic-type-operator-abstract'
import { type TrimmerAttributes } from './dynamic-type-operator-trimmer'
import { OperatorModal } from '../../components/operator-modal'

export const TrimmerConfigModal = (props: OperatorConfigModalProps<TrimmerAttributes>): React.JSX.Element => {
  const { t } = useTranslation()

  return (
    <OperatorModal
      { ...props }
      initialValues={ { trim: 3 } }
    >
      {() => (
        <>
          <Form.Item
            label={ t('data-hub.label') }
            name="label"
          >
            <Input />
          </Form.Item>
          <Form.Item
            label={ t('data-hub.operator.trim') }
            name="trim"
          >
            <Select
              options={ [
                { label: t('data-hub.operator.trim.disabled'), value: 0 },
                { label: t('data-hub.operator.trim.left'), value: 1 },
                { label: t('data-hub.operator.trim.right'), value: 2 },
                { label: t('data-hub.operator.trim.both'), value: 3 }
              ] }
            />
          </Form.Item>
        </>
      )}
    </OperatorModal>
  )
}
