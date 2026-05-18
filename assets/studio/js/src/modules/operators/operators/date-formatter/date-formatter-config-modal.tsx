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
import { Input, Form, Flex, Button, IconTextButton } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type OperatorConfigModalProps } from '../../dynamic-type-operator-abstract'
import { type DateFormatterAttributes } from './dynamic-type-operator-date-formatter'
import { OperatorModal } from '../../components/operator-modal'

export const DateFormatterConfigModal = (props: OperatorConfigModalProps<DateFormatterAttributes>): React.JSX.Element => {
  const { t } = useTranslation()

  const openHelp = (): void => {
    window.open('https://www.php.net/manual/en/function.date.php', '_blank', 'noopener,noreferrer')
  }

  return (
    <OperatorModal
      { ...props }
      footer={ ({ handleApply, onCancel, disabled }) => (
        disabled
          ? null
          : (
            <Flex justify="space-between">
              <IconTextButton
                icon={ { value: 'help-circle' } }
                onClick={ openHelp }
                type="default"
              >
                {t('data-hub.help')}
              </IconTextButton>

              <Flex gap="small">
                <Button onClick={ onCancel }>
                  {t('cancel')}
                </Button>
                <Button
                  onClick={ () => { void handleApply() } }
                  type="primary"
                >
                  {t('apply')}
                </Button>
              </Flex>
            </Flex>
            )
      ) }
      initialValues={ { format: 'Y-m-d H:i:s' } }
      size="M"
    >
      {() => (
        <>
          <Form.Item
            label={ t('data-hub.label') }
            name="label"
          >
            <Input maxLength={ 255 } />
          </Form.Item>

          <Form.Item
            label={ t('data-hub.operator.dateformatter.format') }
            name="format"
          >
            <Input maxLength={ 255 } />
          </Form.Item>
        </>
      )}
    </OperatorModal>
  )
}
