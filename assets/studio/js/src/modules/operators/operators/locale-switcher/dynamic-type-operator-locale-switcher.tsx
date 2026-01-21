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
import { Text } from '@pimcore/studio-ui-bundle/components'
import { DynamicTypeOperatorAbstract, type OperatorConfigModalProps, type ColumnConfig } from '../../dynamic-type-operator-abstract'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { LocaleSwitcherConfigModal } from './locale-switcher-config-modal'
import { isNil } from 'lodash'

export interface LocaleSwitcherAttributes {
  label: string
  locale: string
}

export class DynamicTypeOperatorLocaleSwitcher extends DynamicTypeOperatorAbstract<LocaleSwitcherAttributes> {
  readonly id = 'LocaleSwitcher'

  getIcon (): ElementIcon {
    return {
      type: 'name',
      value: 'globe-02'
    }
  }

  getLabel (config: ColumnConfig<LocaleSwitcherAttributes>): React.ReactNode {
    const label = config.attributes.label
    const locale = config.attributes.locale

    if (isNil(locale)) {
      return <Text>{label}</Text>
    }

    return (
      <>
        {label} <Text type="secondary">({locale})</Text>
      </>
    )
  }

  getConfigModal (props: OperatorConfigModalProps<LocaleSwitcherAttributes>): React.JSX.Element {
    return <LocaleSwitcherConfigModal { ...props } />
  }
}
