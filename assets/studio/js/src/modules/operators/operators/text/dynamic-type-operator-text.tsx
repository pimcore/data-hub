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
import { isNonEmptyString } from '@pimcore/studio-ui-bundle/utils'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeOperatorAbstract, type OperatorConfigModalProps, type ColumnConfig } from '../../dynamic-type-operator-abstract'
import { TextConfigModal } from './text-config-modal'

export interface TextAttributes {
  label: string
  textValue: string
}

@injectable()
export class DynamicTypeOperatorText extends DynamicTypeOperatorAbstract<TextAttributes> {
  readonly id = 'Text'

  getIcon (): ElementIcon {
    return { type: 'name', value: 'text' }
  }

  getLabel (config: ColumnConfig<TextAttributes>, localizedName: string): React.ReactNode {
    const textValue = config.attributes.textValue
    return isNonEmptyString(textValue) ? textValue : localizedName
  }

  getConfigModal (props: OperatorConfigModalProps<TextAttributes>): React.JSX.Element {
    return <TextConfigModal { ...props } />
  }

  getGroup (): string {
    return 'formatter'
  }

  getSubGroup (): string | undefined {
    return 'string'
  }

  allowsChildren (): boolean {
    return false
  }
}
