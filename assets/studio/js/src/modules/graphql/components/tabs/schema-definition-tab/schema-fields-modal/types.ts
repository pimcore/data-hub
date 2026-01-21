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

export interface FieldAttribute {
  attribute?: string
  label: string
  dataType: string
  layout?: Record<string, any>
  type?: string
  class?: string
  children?: FieldAttribute[]
  key?: string
}

export interface ColumnConfig {
  attributes: FieldAttribute
  isOperator: boolean
  key?: string
  label?: string
}

export interface EntityColumnConfig {
  columns: ColumnConfig[]
}

export interface QueryEntityConfig {
  id: string
  name: string
  columnConfig?: EntityColumnConfig
}

export interface TreeNode extends TreeDataItem {
  dataType?: string
  isOperator?: boolean
  label?: string
  columnConfig?: ColumnConfig
  childIndex?: number
}
