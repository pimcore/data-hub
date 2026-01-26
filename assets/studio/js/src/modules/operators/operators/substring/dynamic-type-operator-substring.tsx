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
import { SubstringConfigModal } from './substring-config-modal'

export interface SubstringAttributes {
  label: string
  start: number
  length?: number
  ellipses?: boolean
}

@injectable()
export class DynamicTypeOperatorSubstring extends DynamicTypeOperatorAbstract<SubstringAttributes> {
  readonly id = 'Substring'

  getIcon (): ElementIcon {
    return { type: 'name', value: 'text' }
  }

  getLabel (config: ColumnConfig<SubstringAttributes>, localizedName: string): React.ReactNode {
    const label = config.attributes.label
    const start = config.attributes.start
    const length = config.attributes.length
    const displayLabel = isNonEmptyString(label) ? label : localizedName

    if (!isNil(start) || !isNil(length)) {
      return (
        <>
          {displayLabel} <Text type="secondary">({start},{length ?? '∞'})</Text>
        </>
      )
    }

    return displayLabel
  }

  getConfigModal (props: OperatorConfigModalProps<SubstringAttributes>): React.JSX.Element {
    return <SubstringConfigModal { ...props } />
  }

  getGroup (): string {
    return 'transformer'
  }

  getSubGroup (): string | undefined {
    return 'string'
  }

  getMaxChildCount (): number {
    return 1
  }
}
