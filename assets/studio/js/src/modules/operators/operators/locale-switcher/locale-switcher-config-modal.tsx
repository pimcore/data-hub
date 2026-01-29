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
import { Input, Form, Select } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type OperatorConfigModalProps } from '../../dynamic-type-operator-abstract'
import { useSettings } from '@pimcore/studio-ui-bundle/modules/app'
import { useLanguageLookup } from '@pimcore/studio-ui-bundle/modules/translations'
import { type LocaleSwitcherAttributes } from './dynamic-type-operator-locale-switcher'
import { OperatorModal } from '../../components/operator-modal'

export function LocaleSwitcherConfigModal (props: OperatorConfigModalProps<LocaleSwitcherAttributes>): React.JSX.Element {
  const { t } = useTranslation()
  const settings = useSettings()
  const { getDisplayName } = useLanguageLookup()

  const availableLanguages = (settings.validLanguages ?? []).map((locale: string) => ({
    value: locale,
    label: `${getDisplayName(locale)} [${locale}]`
  }))

  return (
    <OperatorModal
      { ...props }
      initialValues={ { locale: '' } }
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
            label={ t('data-hub.locale') }
            name="locale"
          >
            <Select
              options={ availableLanguages }
              placeholder={ t('data-hub.locale.select') }
            />
          </Form.Item>
        </>
      )}
    </OperatorModal>
  )
}
