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
import { isNonEmptyString } from '@pimcore/studio-ui-bundle/utils'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeOperatorAbstract, type OperatorConfigModalProps, type ColumnConfig } from '../../dynamic-type-operator-abstract'
import { IfEmptyConfigModal } from './if-empty-config-modal'

export interface IfEmptyAttributes {
  label: string
}

@injectable()
export class DynamicTypeOperatorIfEmpty extends DynamicTypeOperatorAbstract<IfEmptyAttributes> {
  readonly id = 'IfEmpty'

  getIcon (): ElementIcon {
    return { type: 'name', value: 'question-circle' }
  }

  getLabel (config: ColumnConfig<IfEmptyAttributes>, localizedName: string): React.ReactNode {
    const label = config.attributes.label
    return isNonEmptyString(label) ? label : localizedName
  }

  getGroup (): string {
    return 'other'
  }

  getConfigModal (props: OperatorConfigModalProps<IfEmptyAttributes>): React.JSX.Element {
    return <IfEmptyConfigModal { ...props } />
  }

  getMaxChildCount (): number {
    return 1
  }
}
