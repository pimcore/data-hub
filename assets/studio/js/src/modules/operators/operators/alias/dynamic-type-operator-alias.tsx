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
import { AliasConfigModal } from './alias-config-modal'

export interface AliasAttributes {
  label: string
}

@injectable()
export class DynamicTypeOperatorAlias extends DynamicTypeOperatorAbstract<AliasAttributes> {
  readonly id = 'Alias'

  getIcon (): ElementIcon {
    return { type: 'name', value: 'alias' }
  }

  getLabel (config: ColumnConfig<AliasAttributes>, localizedName: string): React.ReactNode {
    const label = config.attributes.label
    return isNonEmptyString(label) ? label : localizedName
  }

  getConfigModal (props: OperatorConfigModalProps<AliasAttributes>): React.JSX.Element {
    return <AliasConfigModal { ...props } />
  }

  getGroup (): string {
    return 'other'
  }

  getMaxChildCount (): number {
    return 1
  }
}
