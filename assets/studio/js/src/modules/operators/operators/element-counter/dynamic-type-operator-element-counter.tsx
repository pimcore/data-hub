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
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { isNonEmptyString } from '@pimcore/studio-ui-bundle/utils'
import { DynamicTypeOperatorAbstract, type OperatorConfigModalProps, type ColumnConfig } from '../../dynamic-type-operator-abstract'
import { ElementCounterConfigModal } from './element-counter-config-modal'

export interface ElementCounterAttributes {
  label: string
  countEmpty: boolean
}

@injectable()
export class DynamicTypeOperatorElementCounter extends DynamicTypeOperatorAbstract<ElementCounterAttributes> {
  readonly id = 'ElementCounter'

  getIcon (): ElementIcon {
    return { type: 'name', value: 'number' }
  }

  getLabel (config: ColumnConfig<ElementCounterAttributes>, localizedName: string): React.ReactNode {
    const label = config.attributes.label
    return isNonEmptyString(label) ? label : localizedName
  }

  getConfigModal (props: OperatorConfigModalProps<ElementCounterAttributes>): React.JSX.Element {
    return <ElementCounterConfigModal { ...props } />
  }

  getGroup (): string {
    return 'transformer'
  }

  getSubGroup (): string | undefined {
    return 'other'
  }

  allowsChildren (): boolean {
    return true
  }
}
