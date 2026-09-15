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

// the same four words in the same four colours Studio's own form annotation uses, so a
// reader sees one vocabulary whether they look at the rail, a row header or a field
const STATUS_COLOUR: Record<ChangeStatus, string> = {
  added: 'green',
  changed: 'gold',
  removed: 'red',
  moved: 'geekblue'
}

export interface StatusTagProps {
  readonly status: ChangeStatus
}

/**
 * The four words are this bundle's, not the caller's: an adapter that could pass its own
 * wording is an adapter that can disagree with the next one, which is the thing this exists
 * to prevent.
 */
export const StatusTag: React.FC<StatusTagProps> = ({ status }) => {
  const { t } = useTranslation()

  // no trailing margin: the tag ends a row, and antd's default gap would push it off the edge
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
