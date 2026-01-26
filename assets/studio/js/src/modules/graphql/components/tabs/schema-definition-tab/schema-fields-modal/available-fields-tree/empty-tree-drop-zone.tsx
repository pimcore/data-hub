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

  const checkForValidContext = useCallback((info: DragInfo): boolean => {
    return isValidDragType(info)
  }, [isValidDragType])

  const checkForValidData = useCallback((info: DragInfo): boolean => {
    return canDropToRoot(info)
  }, [canDropToRoot])

  const onDrop = useCallback((info: DragInfo): void => {
    handleDropToRoot(info)
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
