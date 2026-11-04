/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useEffect } from 'react'
import { usePreviewMode } from '@pimcore/studio-ui-bundle/components'
import { api } from '@pimcore/studio-ui-bundle/api/data-object'

const SYSTEM_COLUMNS = [
  { key: 'id', type: 'system.id', group: ['system'] as string[], config: [] as never[] },
  { key: 'fullpath', type: 'system.string', group: ['system'] as string[], config: [] as never[] }
]

/**
 * While preview mode is on and no item is picked, preselects the first object of the class.
 * Nothing is requested while preview mode is off or an item is picked. Returns whether preview mode is on.
 */
export const usePreviewItemPreselect = (classId: string): boolean => {
  const { enabled, item, setItem } = usePreviewMode()

  const { currentData } = api.endpoints.dataObjectGetGrid.useQuery(
    { classId, body: { folderId: 1, columns: SYSTEM_COLUMNS, filters: { includeDescendants: true, page: 1, pageSize: 1 } } },
    { skip: !enabled || item !== null }
  )

  const first = currentData?.items?.[0]

  useEffect(() => {
    if (!enabled || item !== null || first?.id === undefined) return
    const fullpath = first.columns?.find((column: { key: string }) => column.key === 'fullpath')?.value
    setItem({ elementType: 'data-object', data: { id: first.id as number, ...(typeof fullpath === 'string' ? { fullpath } : {}) } })
  }, [enabled, item, first])

  return enabled
}
