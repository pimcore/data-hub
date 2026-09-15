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

/** this bundle ships the four words, so an adapter needs no translations of its own */
const DEFAULT_PREFIX = 'data-hub.review.status'

export interface StatusTagProps {
  readonly status: ChangeStatus
  /**
   * Where to read the four words from. An adapter that wants its own wording passes its own
   * prefix; leaving it alone is what keeps the vocabulary the same everywhere.
   */
  readonly translationPrefix?: string
}

export const StatusTag: React.FC<StatusTagProps> = ({ status, translationPrefix = DEFAULT_PREFIX }) => {
  const { t } = useTranslation()

  // no trailing margin: the tag ends a row, and antd's default gap would push it off the edge
  return (
    <Tag
      color={ STATUS_COLOUR[status] }
      data-review-mark=""
      style={ { marginInlineEnd: 0 } }
    >
      { t(`${translationPrefix}.${status}`) }
    </Tag>
  )
}
