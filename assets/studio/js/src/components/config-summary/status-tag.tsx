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
import { Tag } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'

export type ChangeStatus = 'added' | 'changed' | 'removed' | 'moved'

// the colours Studio's form annotation uses for the same words
const STATUS_COLOUR: Record<ChangeStatus, string> = {
  added: 'green',
  changed: 'gold',
  removed: 'red',
  moved: 'geekblue'
}

export interface StatusTagProps {
  readonly status: ChangeStatus
}

/** the status words are this bundle's, so adapters cannot disagree on wording */
export const StatusTag: React.FC<StatusTagProps> = ({ status }) => {
  const { t } = useTranslation()

  // the tag ends its row; antd's trailing margin would overhang
  return (
    <Tag
      color={ STATUS_COLOUR[status] }
      data-review-mark=""
      style={ { marginInlineEnd: 0 } }
    >
      { t(`data-hub.review.status.${status}`) }
    </Tag>
  )
}
