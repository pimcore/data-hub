/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useMemo } from 'react'
import { HotspotDroppable, Draggable, type HotspotArea } from '@pimcore/studio-ui-bundle/components'
import { useTreeContext, type DragInfo } from './hooks/use-tree-context'
import { type InternalTreeNode, createTreeItem } from './tree-item/tree-item'
import { DragType, DropPosition } from '../drag-types'

interface TreeNodeRendererProps {
  itemData: InternalTreeNode
  initialComponent: React.ReactNode
}

export const TreeNodeRenderer = ({
  itemData,
  initialComponent
}: TreeNodeRendererProps): React.JSX.Element => {
  const {
    operatorRegistry,
    fieldDefinitionRegistry,
    canDrop,
    isValidDragType,
    handleDrop
  } = useTreeContext()

  const nodeKey = itemData.key
  const treeItem = createTreeItem(itemData, operatorRegistry)

  const isSelfDrag = (info: DragInfo): boolean => {
    return info.type === DragType.TREE_ITEM && info.data.key === nodeKey
  }

  const hotspots: HotspotArea[] = useMemo(() => {
    return [
      {
        id: 'sorting-top',
        className: 'dnd__sorting dnd__sorting--top',
        isValidContext: (info: DragInfo) => isValidDragType(info),
        isValidData: (info: DragInfo) => !isSelfDrag(info) && canDrop(info, nodeKey, DropPosition.BEFORE),
        position: { x: 0, y: 0, width: '100%', height: '30%' },
        onDrop: (info: DragInfo) => { handleDrop(info, nodeKey, DropPosition.BEFORE) }
      },
      {
        id: 'drop-middle',
        isValidContext: (info: DragInfo) => isValidDragType(info),
        isValidData: (info: DragInfo) => !isSelfDrag(info) && treeItem.canHaveChildren() && canDrop(info, nodeKey, DropPosition.INTO),
        position: { x: '0', y: '30%', width: '100%', height: '40%' },
        onDrop: (info: DragInfo) => { handleDrop(info, nodeKey, DropPosition.INTO) }
      },
      {
        id: 'sorting-bottom',
        className: 'dnd__sorting dnd__sorting--bottom',
        isValidContext: (info: DragInfo) => isValidDragType(info),
        isValidData: (info: DragInfo) => !isSelfDrag(info) && canDrop(info, nodeKey, DropPosition.AFTER),
        position: { x: 0, y: '70%', width: '100%', height: '30%' },
        onDrop: (info: DragInfo) => { handleDrop(info, nodeKey, DropPosition.AFTER) }
      }
    ]
  }, [nodeKey, treeItem, isValidDragType, canDrop, handleDrop])

  const iconProps = useMemo((): { value: string } => {
    if (itemData.isOperator && itemData.attributes.class !== undefined) {
      const opType = operatorRegistry.getDynamicType(String(itemData.attributes.class), false)
      return opType?.getIcon() ?? { value: 'field' }
    }
    if (itemData.attributes.dataType !== undefined) {
      const fieldDef = fieldDefinitionRegistry.getDynamicType(String(itemData.attributes.dataType), false)
      return fieldDef?.getIcon() ?? { value: 'field' }
    }
    return { value: 'field' }
  }, [itemData, operatorRegistry, fieldDefinitionRegistry])

  const dragInfo = useMemo(() => ({
    type: DragType.TREE_ITEM,
    data: {
      key: nodeKey,
      isOperator: itemData.isOperator,
      title: String(itemData.attributes.label ?? itemData.attributes.attribute ?? ''),
      dataType: itemData.attributes.dataType,
      operatorClass: itemData.attributes.class
    },
    icon: iconProps,
    title: String(itemData.attributes.label ?? itemData.attributes.attribute ?? '')
  }), [nodeKey, itemData, iconProps])

  return (
    <Draggable info={ dragInfo }>
      <HotspotDroppable
        disableDndActiveIndicator
        hotspots={ hotspots }
      >
        {initialComponent}
      </HotspotDroppable>
    </Draggable>
  )
}
