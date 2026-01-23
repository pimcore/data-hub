/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { forwardRef, useCallback } from 'react'
import { Droppable, useDroppable } from '@pimcore/studio-ui-bundle/components'
import { useTreeContext, type DragInfo } from './hooks/use-tree-context'

const buildDragInfo = (info: unknown): DragInfo => {
  const rawInfo = info as Record<string, unknown>
  if (rawInfo.type === 'available-field') {
    return { type: 'tree-item', data: rawInfo.data as DragInfo['data'] }
  }
  return rawInfo as unknown as DragInfo
}

const EmptyTreeDropContent = forwardRef<HTMLDivElement>(function EmptyTreeDropContent (props, ref): React.JSX.Element {
  const { getStateClasses } = useDroppable()
  const stateClasses = getStateClasses()

  return (
    <div
      className={ stateClasses.join(' ') }
      ref={ ref }
      style={ {
        padding: '32px 16px',
        textAlign: 'center',
        color: '#999',
        border: '2px dashed #d9d9d9',
        borderRadius: '4px',
        minHeight: '100px',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center'
      } }
    >
      Drag fields or operators here
    </div>
  )
})

export const EmptyTreeDropZone = (): React.JSX.Element => {
  const {
    isValidDragType,
    canDropToRoot,
    handleDropToRoot
  } = useTreeContext()

  const checkForValidContext = useCallback((info: unknown): boolean => {
    return isValidDragType(buildDragInfo(info))
  }, [isValidDragType])

  const checkForValidData = useCallback((info: unknown): boolean => {
    return canDropToRoot(buildDragInfo(info))
  }, [canDropToRoot])

  const onDrop = useCallback((info: unknown): void => {
    handleDropToRoot(buildDragInfo(info))
  }, [handleDropToRoot])

  return (
    <Droppable
      isValidContext={ checkForValidContext }
      isValidData={ checkForValidData }
      onDrop={ onDrop }
    >
      <EmptyTreeDropContent />
    </Droppable>
  )
}
