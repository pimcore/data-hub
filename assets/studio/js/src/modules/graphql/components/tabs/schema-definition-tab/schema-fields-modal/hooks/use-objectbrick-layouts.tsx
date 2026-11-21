/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useState, useEffect } from 'react'
import { useAppDispatch } from '@pimcore/studio-ui-bundle/app'
import { api, type ConfigLayoutDefinition } from '@pimcore/studio-ui-bundle/api/class-definition'
import { collectBrickLayoutResults, type BrickLayoutLoadError } from './class-layout-load-error'

export type { BrickLayoutLoadError } from './class-layout-load-error'

interface UseObjectBrickLayoutsReturn {
  layouts: Map<string, ConfigLayoutDefinition>
  isLoading: boolean
  loadErrors: BrickLayoutLoadError[]
}

export const useObjectBrickLayouts = (brickKeys: string[]): UseObjectBrickLayoutsReturn => {
  const dispatch = useAppDispatch()
  const [layouts, setLayouts] = useState<Map<string, ConfigLayoutDefinition>>(new Map())
  const [loadErrors, setLoadErrors] = useState<BrickLayoutLoadError[]>([])
  // Start as true when keys are already known on first render — prevents a flash of the
  // incomplete tree between "class layout resolved" and "brick fetch effect fires".
  const [isLoading, setIsLoading] = useState(brickKeys.length > 0)

  // Stable serialization used as effect dependency — avoids re-firing on same keys with new array reference
  const sortedKeysString = [...brickKeys].sort((a, b) => a.localeCompare(b)).join(',')

  useEffect(() => {
    if (brickKeys.length === 0) {
      setLayouts(new Map())
      setLoadErrors([])
      setIsLoading(false)
      return
    }

    setIsLoading(true)
    let cancelled = false

    const promises = brickKeys.map(key =>
      dispatch(api.endpoints.classObjectBrickGetLayoutByKey.initiate({ key }))
    )

    void Promise.all(promises).then((responses: Array<{ data?: ConfigLayoutDefinition, error?: unknown }>) => {
      if (cancelled) return

      const { layouts: newLayouts, loadErrors: newLoadErrors } = collectBrickLayoutResults(brickKeys, responses)

      setLayouts(newLayouts)
      setLoadErrors(newLoadErrors)
      setIsLoading(false)
    })

    return () => {
      cancelled = true
      promises.forEach(p => { p.unsubscribe() })
    }
  }, [dispatch, sortedKeysString])

  return { layouts, isLoading, loadErrors }
}
