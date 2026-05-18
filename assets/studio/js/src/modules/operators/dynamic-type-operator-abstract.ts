/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import type React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'

export interface ColumnConfig<TAttributes = any> {
  attributes: TAttributes
  isOperator: boolean
  key?: string
  label?: string
}

export interface SourceConfig {
  dataType?: string
  isOperator?: boolean
  operatorClass?: string
}

export interface OperatorConfigModalProps<TAttributes = any> {
  config: ColumnConfig<TAttributes>
  operator: DynamicTypeOperatorAbstract<TAttributes>
  disabled?: boolean
  onApply: (config: ColumnConfig<TAttributes>) => void
  onCancel: () => void
}

@injectable()
export abstract class DynamicTypeOperatorAbstract<TAttributes = any> {
  abstract readonly id: string

  abstract getIcon (): ElementIcon

  abstract getLabel (config: ColumnConfig<TAttributes>, localizedName: string): React.ReactNode

  abstract getConfigModal (props: OperatorConfigModalProps<TAttributes>): React.JSX.Element | null

  getGroup (): string {
    return 'other'
  }

  getSubGroup (): string | undefined {
    return undefined
  }

  // Return undefined for unlimited children or a number to enforce a limit
  getMaxChildCount (): number | undefined {
    return undefined
  }

  allowChild (targetConfig: ColumnConfig<TAttributes>, sourceConfig: SourceConfig): boolean {
    const maxChildCount = this.getMaxChildCount()

    if (maxChildCount !== undefined) {
      const attributes = targetConfig.attributes as any
      const currentChildCount = Array.isArray(attributes?.children)
        ? attributes.children.length
        : 0

      if (currentChildCount >= maxChildCount) {
        return false
      }
    }

    const allowedTypes = this.allowedChildTypes()
    if (allowedTypes.length > 0 && sourceConfig?.dataType !== undefined) {
      return allowedTypes.includes(sourceConfig.dataType)
    }

    return true
  }

  allowsChildren (): boolean {
    return true
  }

  // Return empty array for no restrictions on child data types
  allowedChildTypes (): string[] {
    return []
  }
}
