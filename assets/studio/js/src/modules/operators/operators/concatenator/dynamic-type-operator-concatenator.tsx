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
import { ConcatenatorConfigModal } from './concatenator-config-modal'

export interface ConcatenatorAttributes {
  label: string
  glue?: string
}

@injectable()
export class DynamicTypeOperatorConcatenator extends DynamicTypeOperatorAbstract<ConcatenatorAttributes> {
  readonly id = 'Concatenator'

  getIcon (): ElementIcon {
    return { type: 'name', value: 'operator-concatenator' }
  }

  getLabel (config: ColumnConfig<ConcatenatorAttributes>, localizedName: string): React.ReactNode {
    const label = config.attributes.label
    const glue = config.attributes.glue
    const displayLabel = isNonEmptyString(label) ? label : localizedName

    if (!isNil(glue) && glue !== '') {
      return (
        <>
          {displayLabel} <Text type="secondary">({glue})</Text>
        </>
      )
    }

    return displayLabel
  }

  getConfigModal (props: OperatorConfigModalProps<ConcatenatorAttributes>): React.JSX.Element {
    return <ConcatenatorConfigModal { ...props } />
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
