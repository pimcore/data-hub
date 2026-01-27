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
import { Droppable, useDroppable, Flex } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { useTreeContext, type DragInfo } from './hooks/use-tree-context'
import { useStyles } from './empty-tree-drop-zone.styles'
import cn from 'classnames'

const EmptyTreeDropContent = forwardRef<HTMLDivElement>(function EmptyTreeDropContent (props, ref): React.JSX.Element {
  const { t } = useTranslation()
  const { getStateClasses } = useDroppable()
  const { styles } = useStyles()
  const stateClasses = getStateClasses()

  return (
    <Flex
      align="center"
      className={ cn(styles.dropZone, stateClasses.join(' ')) }
      justify="center"
      ref={ ref }
    >
      {t('data-hub.schema.drag-class-attributes-or-operators')}
    </Flex>
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
