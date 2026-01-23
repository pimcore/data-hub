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
import { type TreeNode } from '../types'
import { useAvailableFieldsContext } from './available-fields-context'

interface TreeNodeTitleRendererProps {
  node: TreeNode
  initialComponent: React.ReactNode
}

export const TreeNodeTitleRenderer = ({
  node,
  initialComponent
}: TreeNodeTitleRendererProps): React.JSX.Element => {
  // Get everything from context
  const {
    operatorRegistry,
    fieldDefinitionRegistry,
    isValidContext,
    isValidDropIntoOperator,
    isValidSiblingDrop,
    onDrop
  } = useAvailableFieldsContext()

  const nodeKey = String(node.key)

  const hotspots: HotspotArea[] = useMemo(() => [
    {
      id: 'sorting-top',
      className: 'dnd__sorting dnd__sorting--top',
      isValidContext: (info: any) => isValidContext(info, nodeKey),
      isValidData: () => isValidSiblingDrop(node),
      position: { x: 0, y: 0, width: '100%', height: '30%' },
      onDrop: (info: any) => { onDrop(info, nodeKey, 'before', false) }
    },
    {
      id: 'drop-middle',
      isValidContext: (info: any) => isValidContext(info, nodeKey),
      isValidData: () => isValidDropIntoOperator(node),
      position: { x: '0', y: '30%', width: '100%', height: '40%' },
      onDrop: (info: any) => { onDrop(info, nodeKey, 'after', true) }
    },
    {
      id: 'sorting-bottom',
      className: 'dnd__sorting dnd__sorting--bottom',
      isValidContext: (info: any) => isValidContext(info, nodeKey),
      isValidData: () => isValidSiblingDrop(node),
      position: { x: 0, y: '70%', width: '100%', height: '30%' },
      onDrop: (info: any) => { onDrop(info, nodeKey, 'after', false) }
    }
  ], [nodeKey, node, isValidContext, isValidDropIntoOperator, isValidSiblingDrop, onDrop])

  // Get icon props for drag info - memoized
  const iconProps = useMemo((): { value: string } => {
    if (node.isOperator === true && node.columnConfig?.attributes?.class !== undefined) {
      const opType = operatorRegistry.getDynamicType(node.columnConfig.attributes.class, false)
      return opType?.getIcon() ?? { value: 'field' }
    }
    if (node.dataType !== undefined) {
      const fieldDef = fieldDefinitionRegistry.getDynamicType(node.dataType, false)
      return fieldDef?.getIcon() ?? { value: 'field' }
    }
    return { value: 'field' }
  }, [node.isOperator, node.columnConfig?.attributes?.class, node.dataType, operatorRegistry, fieldDefinitionRegistry])

  const dragInfo = useMemo(() => ({
    type: 'available-field',
    data: {
      sourceKey: nodeKey,
      sourceIsOperator: node.isOperator === true,
      sourceChildIndex: node.childIndex,
      sourceParentKey: node.columnConfig?.key,
      title: String(node.title),
      dataType: node.dataType,
      operatorClass: node.columnConfig?.attributes?.class
    },
    icon: iconProps,
    title: String(node.title)
  }), [nodeKey, node, iconProps])

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
