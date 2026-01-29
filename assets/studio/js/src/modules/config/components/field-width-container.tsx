/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { type ReactNode } from 'react'
import { useFieldWidth } from '@pimcore/studio-ui-bundle/modules/element'

interface FieldWidthContainerProps {
  children: ReactNode
}

export const FieldWidthContainer = ({ children }: FieldWidthContainerProps): React.JSX.Element => {
  const fieldWidth = useFieldWidth()

  return (
    <div style={ { maxWidth: `${fieldWidth.large}px`, width: '100%' } }>
      {children}
    </div>
  )
}
