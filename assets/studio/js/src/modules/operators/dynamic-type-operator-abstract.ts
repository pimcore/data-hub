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

export interface OperatorConfigModalProps<TAttributes = any> {
  config: ColumnConfig<TAttributes>
  onApply: (config: ColumnConfig<TAttributes>) => void
  onCancel: () => void
}

@injectable()
export abstract class DynamicTypeOperatorAbstract<TAttributes = any> {
  /**
   * Unique identifier for the operator type (e.g., 'DateFormatter', 'Alias', 'Substring')
   */
  abstract readonly id: string

  /**
   * Get the icon props to display in the tree for this operator
   */
  abstract getIcon (): ElementIcon

  /**
   * Get the display label for the tree node
   */
  abstract getLabel (config: ColumnConfig<TAttributes>): React.ReactNode

  /**
   * Get the configuration modal component for this operator
   */
  abstract getConfigModal (props: OperatorConfigModalProps<TAttributes>): React.JSX.Element | null

  /**
   * Get the translation key for the operator name
   */
  getNameTranslationKey (): string {
    return `data-hub.operator.${this.id.toLowerCase()}`
  }

  /**
   * Get the translation key for the operator description
   */
  getDescriptionTranslationKey (): string {
    return `data-hub.operator.${this.id.toLowerCase()}.description`
  }

  /**
   * Get the translation key for the operator group
   */
  getGroupTranslationKey (): string {
    return 'data-hub.operator.group.other'
  }

  /**
   * Get the optional subgroup key for organizing operators into folders
   * Return undefined for no subgroup
   */
  getSubGroupKey (): string | undefined {
    return 'data-hub.operator.subgroup.other'
  }

  /**
   * Get the maximum number of children this operator can have
   * Return undefined for unlimited children
   * Return a number to enforce a specific limit
   */
  getMaxChildCount (): number | undefined {
    return undefined
  }

  /**
   * Check if this operator can accept a child
   * Similar to ExtJS allowChild function
   *
   * @param config - The operator's current configuration
   * @returns true if a child can be added, false otherwise
   */
  allowChild (config: ColumnConfig<TAttributes>): boolean {
    const maxChildCount = this.getMaxChildCount()

    // If no limit is defined, always allow children
    if (maxChildCount === undefined) {
      return true
    }

    // Check current child count against limit
    // Use any cast to access children property which may not be in generic TAttributes
    const attributes = config.attributes as any
    const currentChildCount = Array.isArray(attributes?.children)
      ? attributes.children.length
      : 0

    return currentChildCount < maxChildCount
  }

  /**
   * Check if this operator type allows children at all
   * Override this to return false for operators that cannot have children
   */
  allowsChildren (): boolean {
    return true
  }
}
