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
import { Draggable } from '@pimcore/studio-ui-bundle/components'
import { isNil } from 'lodash'
import { DragType } from '../drag-types'
import { type TreeNode } from '../types'

interface DraggableTreeTitleProps {
  node: TreeNode
  initialComponent: React.ReactNode
}

/**
 * Wraps tree node titles with drag capability for the class attributes sidebar.
 * Only leaf nodes are draggable.
 */
export const DraggableTreeTitle = ({
  node,
  initialComponent
}: DraggableTreeTitleProps): React.JSX.Element => {
  const isLeaf = node.isLeaf === true || (isNil(node.children) || node.children.length === 0)

  if (!isLeaf) {
    return <>{initialComponent}</>
  }

  return (
    <Draggable
      info={ {
        type: DragType.CLASS_ATTRIBUTE,
        data: {
          key: String(node.key),
          attribute: node.attribute,
          title: String(node.title),
          dataType: String(node.dataType ?? 'text')
        },
        icon: node.iconProps,
        title: String(node.title)
      } }
    >
      {initialComponent}
    </Draggable>
  )
}
