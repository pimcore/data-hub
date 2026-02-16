/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type SourceConfig } from '../../../../../../operators/dynamic-type-operator-abstract'
import { isNil } from 'lodash'
import { DragType } from '../drag-types'

interface DragInfo {
  type: string
  data?: {
    dataType?: string
    operatorId?: string
    [key: string]: any
  }
}

interface TreeItemAttributes {
  dataType?: string
  class?: string
  [key: string]: any
}

export function createSourceConfigFromDragInfo (dragInfo?: DragInfo): SourceConfig {
  return {
    dataType: !isNil(dragInfo) && dragInfo.type === DragType.CLASS_ATTRIBUTE && !isNil(dragInfo.data?.dataType)
      ? String(dragInfo.data.dataType)
      : undefined,
    isOperator: !isNil(dragInfo) && dragInfo.type === DragType.OPERATOR,
    operatorClass: !isNil(dragInfo) && dragInfo.type === DragType.OPERATOR && !isNil(dragInfo.data?.operatorId)
      ? String(dragInfo.data.operatorId)
      : undefined
  }
}

export function createSourceConfigFromAttributes (attributes: TreeItemAttributes, isOperator: boolean): SourceConfig {
  return {
    dataType: attributes.dataType,
    isOperator,
    operatorClass: isOperator && !isNil(attributes.class) ? String(attributes.class) : undefined
  }
}
