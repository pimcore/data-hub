/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { isNil } from 'lodash'
import { useCallback, useEffect, useState } from 'react'

export interface UseRefreshIntervalReturn {
  refreshInterval: string | undefined
  setRefreshInterval: (value: string) => void
}

export const useRefreshInterval = (onRefresh: () => void): UseRefreshIntervalReturn => {
  const [refreshInterval, setRefreshInterval] = useState<string | undefined>(undefined)

  const stableOnRefresh = useCallback(onRefresh, [onRefresh])

  useEffect(() => {
    if (isNil(refreshInterval)) {
      return
    }

    const intervalMs = Number.parseInt(refreshInterval) * 1000
    const intervalId = setInterval(() => {
      stableOnRefresh()
    }, intervalMs)

    return () => {
      clearInterval(intervalId)
    }
  }, [refreshInterval, stableOnRefresh])

  return { refreshInterval, setRefreshInterval }
}
