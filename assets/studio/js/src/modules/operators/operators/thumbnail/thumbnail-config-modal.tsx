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
import { type ThumbnailAttributes } from './dynamic-type-operator-thumbnail'
import { type OperatorConfigModalProps } from '../../dynamic-type-operator-abstract'
import { OperatorModal } from '../../components/operator-modal'

export const ThumbnailConfigModal = (props: OperatorConfigModalProps<ThumbnailAttributes>): React.JSX.Element => {
  const { t } = useTranslation()

  return (
    <OperatorModal { ...props }>
      {() => (
        <>
          <Form.Item
            label={ t('data-hub.label') }
            name="label"
          >
            <Input />
          </Form.Item>

          <Form.Item
            label={ t('data-hub.thumbnail') }
            name="thumbnailConfig"
          >
            <Input placeholder={ t('data-hub.operator.thumbnail.thumbnail-name') } />
          </Form.Item>
        </>
      )}
    </OperatorModal>
  )
}
