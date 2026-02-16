/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { uuid } from '@pimcore/studio-ui-bundle/utils'
import { isNil } from 'lodash'
import { type InternalTreeNode, type TreeItemAttributes } from '../tree-item/tree-item'
import { type PersistedColumnConfig, type FieldAttribute } from '../../types'
import { DragType, type DragInfo } from '../../drag-types'

export function persistedColumnsToInternalNodes (columns: PersistedColumnConfig[]): InternalTreeNode[] {
  return columns.map(col => {
    const { children: sourceChildren, ...restAttributes } = col.attributes as FieldAttribute & { children?: PersistedColumnConfig[] }
    const attributes: TreeItemAttributes = { ...restAttributes }

    if (!isNil(sourceChildren)) {
      attributes.children = persistedColumnsToInternalNodes(sourceChildren)
    }

    return {
      key: String(col.key ?? ''),
      isOperator: col.isOperator,
      attributes
    }
  })
}

export function internalNodesToPersistedColumns (nodes: InternalTreeNode[]): PersistedColumnConfig[] {
  return nodes.map(node => {
    const { children: sourceChildren, ...restAttributes } = node.attributes
    const attributes: FieldAttribute = {
      label: restAttributes.label ?? '',
      dataType: restAttributes.dataType ?? '',
      ...restAttributes
    }

    if (!isNil(sourceChildren)) {
      Object.assign(attributes, { children: internalNodesToPersistedColumns(sourceChildren) })
    }

    return {
      key: node.key,
      isOperator: node.isOperator,
      attributes
    }
  })
}

export function createItemFromDragInfo (dragInfo: DragInfo): InternalTreeNode | null {
  if (dragInfo.type === DragType.CLASS_ATTRIBUTE) {
    return {
      key: uuid(),
      isOperator: false,
      attributes: {
        attribute: String(dragInfo.data.attribute ?? dragInfo.data.key ?? ''),
        label: String(dragInfo.data.title ?? ''),
        dataType: String(dragInfo.data.dataType ?? 'text')
      }
    }
  }

  if (dragInfo.type === DragType.OPERATOR) {
    return {
      key: uuid(),
      isOperator: true,
      attributes: {
        label: String(dragInfo.data.title ?? ''),
        class: String(dragInfo.data.operatorId ?? ''),
        type: DragType.OPERATOR,
        children: []
      }
    }
  }

  return null
}
