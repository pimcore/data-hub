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
    return { type: 'name', value: 'calendar' }
  }

  getLabel (config: ColumnConfig<DateFormatterAttributes>): React.ReactNode {
    const label = config.attributes.label ?? 'DateFormatter'
    const format = config.attributes.format

    if (!isNil(format)) {
      return (
        <>
          {label} <Text type="secondary">({format})</Text>
        </>
      )
    }

    return label
  }

  getConfigModal (props: OperatorConfigModalProps<DateFormatterAttributes>): React.JSX.Element {
    return <DateFormatterConfigModal { ...props } />
  }

  getGroupTranslationKey (): string {
    return 'data-hub.operator.group.formatter'
  }

  getMaxChildCount (): number {
    return 1
  }
}
