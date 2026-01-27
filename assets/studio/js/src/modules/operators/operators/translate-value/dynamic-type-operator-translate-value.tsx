/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { injectable } from '@pimcore/studio-ui-bundle/app'
import React from 'react'
import { isNil } from 'lodash'
import { Text } from '@pimcore/studio-ui-bundle/components'
import { isNonEmptyString } from '@pimcore/studio-ui-bundle/utils'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeOperatorAbstract, type OperatorConfigModalProps, type ColumnConfig } from '../../dynamic-type-operator-abstract'
import { TranslateValueConfigModal } from './translate-value-config-modal'

export interface TranslateValueAttributes {
  label: string
  prefix?: string
}

@injectable()
export class DynamicTypeOperatorTranslateValue extends DynamicTypeOperatorAbstract<TranslateValueAttributes> {
  readonly id = 'TranslateValue'

  getIcon (): ElementIcon {
    return { type: 'name', value: 'translate' }
  }

  getLabel (config: ColumnConfig<TranslateValueAttributes>, localizedName: string): React.ReactNode {
    const label = config.attributes.label
    const prefix = config.attributes.prefix
    const displayLabel = isNonEmptyString(label) ? label : localizedName

    if (!isNil(prefix) && prefix !== '') {
      return (
        <>
          {displayLabel} <Text type="secondary">({prefix})</Text>
        </>
      )
    }

    return displayLabel
  }

  getGroup (): string {
    return 'transformer'
  }

  getSubGroup (): string | undefined {
    return 'string'
  }

  getConfigModal (props: OperatorConfigModalProps<TranslateValueAttributes>): React.JSX.Element {
    return <TranslateValueConfigModal { ...props } />
  }

  getMaxChildCount (): number {
    return 1
  }
}
