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
import { Draggable, GridButton } from '@pimcore/studio-ui-bundle/components'
import { DragType } from '../drag-types'

interface OperatorGridItemProps {
  groupKey: string
  subGroupKey?: string
  operator: {
    id: string
    icon: any
    localizedName: string
  }
}

export const OperatorGridItem = ({
  groupKey,
  subGroupKey,
  operator
}: OperatorGridItemProps): React.JSX.Element => {
  const dragKey = subGroupKey !== undefined
    ? `${groupKey}-${subGroupKey}-${operator.id}`
    : `${groupKey}-${operator.id}`

  return (
    <Draggable
      info={ {
        type: DragType.OPERATOR,
        data: {
          key: dragKey,
          title: operator.localizedName,
          operatorId: operator.id
        },
        icon: operator.icon,
        title: operator.localizedName
      } }
      key={ dragKey }
    >
      <GridButton
        icon={ operator.icon }
        label={ operator.localizedName }
      />
    </Draggable>
  )
}
