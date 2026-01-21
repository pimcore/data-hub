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
import type { ColumnConfig } from '../components/tabs/schema-definition-tab/schema-fields-modal/types'

export interface OperatorConfigModalProps {
  config: ColumnConfig
  onApply: (config: ColumnConfig) => void
  onCancel: () => void
}

@injectable()
export abstract class DynamicTypeOperatorAbstract {
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
  abstract getLabel (config: ColumnConfig): React.ReactNode

  /**
   * Get the configuration modal component for this operator
   */
  abstract getConfigModal (props: OperatorConfigModalProps): React.JSX.Element | null

  /**
   * Get the translation key for the operator name
   */
  getNameTranslationKey (): string {
    return `data-hub.operator.${this.id.toLowerCase()}`
  }

  /**
   * Get the translation key for the operator group
   */
  getGroupTranslationKey (): string {
    return 'data-hub.operator.group.other'
  }
}
