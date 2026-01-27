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
import { injectable } from '@pimcore/studio-ui-bundle/app'
import { isNil } from 'lodash'
import { Text } from '@pimcore/studio-ui-bundle/components'
import { isNonEmptyString } from '@pimcore/studio-ui-bundle/utils'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeOperatorAbstract, type OperatorConfigModalProps, type ColumnConfig } from '../../dynamic-type-operator-abstract'
import { DateFormatterConfigModal } from './date-formatter-config-modal'

export interface DateFormatterAttributes {
  label: string
  format: string
}

@injectable()
export class DynamicTypeOperatorDateFormatter extends DynamicTypeOperatorAbstract<DateFormatterAttributes> {
  readonly id = 'DateFormatter'

  getIcon (): ElementIcon {
    return { type: 'name', value: 'date-formatter' }
  }

  getLabel (config: ColumnConfig<DateFormatterAttributes>, localizedName: string): React.ReactNode {
    const label = config.attributes.label
    const format = config.attributes.format
    const displayLabel = isNonEmptyString(label) ? label : localizedName

    if (!isNil(format)) {
      return (
        <>
          {displayLabel} <Text type="secondary">({format})</Text>
        </>
      )
    }

    return displayLabel
  }

  getConfigModal (props: OperatorConfigModalProps<DateFormatterAttributes>): React.JSX.Element {
    return <DateFormatterConfigModal { ...props } />
  }

  getGroup (): string {
    return 'formatter'
  }

  getSubGroup (): string | undefined {
    return 'other'
  }

  getMaxChildCount (): number {
    return 1
  }
}
