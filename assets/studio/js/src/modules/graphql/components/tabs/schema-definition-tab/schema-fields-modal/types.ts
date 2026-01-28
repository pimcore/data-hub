/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type TreeDataItem } from '@pimcore/studio-ui-bundle/components'

export interface ChildAttribute {
  key?: string
  attributes?: {
    attribute?: string
    label?: string
    dataType?: string
  }
  isOperator?: boolean
}

export interface FieldAttribute {
  attribute?: string
  label: string
  dataType: string
  layout?: Record<string, any>
  type?: string
  class?: string
  children?: ChildAttribute[]
  key?: string
}

export interface PersistedColumnConfig {
  attributes: FieldAttribute
  isOperator: boolean
  key?: string
  label?: string
}

export interface EntityPersistedColumnConfig {
  columns: PersistedColumnConfig[]
}

export interface QueryEntityConfig {
  id: string
  name: string
  columnConfig?: EntityPersistedColumnConfig
}

export interface TreeNode extends TreeDataItem {
  dataType?: string
  isOperator?: boolean
  label?: string
  columnConfig?: PersistedColumnConfig
  childIndex?: number
  attribute?: string
  iconProps?: any
  operatorId?: string
}
