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
import { injectable, useTranslation } from '@pimcore/studio-ui-bundle/app'
import { isNil } from 'lodash'
import { Text } from '@pimcore/studio-ui-bundle/components'
import { isNonEmptyString } from '@pimcore/studio-ui-bundle/utils'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeOperatorAbstract, type OperatorConfigModalProps, type ColumnConfig } from '../../dynamic-type-operator-abstract'
import { TrimmerConfigModal } from './trimmer-config-modal'

export interface TrimmerAttributes {
  label: string
  trim: number // 0=disabled, 1=left, 2=right, 3=both
}

@injectable()
export class DynamicTypeOperatorTrimmer extends DynamicTypeOperatorAbstract<TrimmerAttributes> {
  readonly id = 'Trimmer'

  getIcon (): ElementIcon {
    return { type: 'name', value: 'trim' }
  }

  getLabel (config: ColumnConfig<TrimmerAttributes>, localizedName: string): React.ReactNode {
    const label = config.attributes.label
    const trim = config.attributes.trim
    const displayLabel = isNonEmptyString(label) ? label : localizedName

    if (!isNil(trim) && trim > 0) {
      const TrimLabel = (): React.JSX.Element => {
        const { t } = useTranslation()
        const modes = ['disabled', 'left', 'right', 'both']
        const mode = modes[trim]

        if (isNil(mode)) {
          return <>{displayLabel}</>
        }

        const translatedMode = t(`data-hub.operator.trim.${mode}`)
        return <>{displayLabel} <Text type="secondary">({translatedMode})</Text></>
      }
      return <TrimLabel />
    }

    return displayLabel
  }

  getConfigModal (props: OperatorConfigModalProps<TrimmerAttributes>): React.JSX.Element {
    return <TrimmerConfigModal { ...props } />
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
