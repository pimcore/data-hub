/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useEffect, useState } from 'react'
import type { ChangeSetReviewData } from '@pimcore/change-control-bundle/sdk'

const CHANGE_SET_API = '/pimcore-studio/api/bundle/change-control/change-sets'

interface ReviewState {
  data?: ChangeSetReviewData
  isLoading: boolean
  error?: unknown
}

/**
 * Reads a change set's review payload, typed by Change Control's own contract.
 *
 * Fetched directly rather than through its RTK endpoints: those are runtime, and the package
 * ships types only - the code lives behind the federation remote, which this bundle must
 * build and run without.
 */
export function useChangeSetReview (changeSetId?: string, contextRef?: string): ReviewState {
  const [state, setState] = useState<ReviewState>({ isLoading: true })

  useEffect(() => {
    if (changeSetId === undefined || changeSetId === '') {
      setState({ isLoading: false, error: new Error('no change set') })
      return
    }

    let cancelled = false
    const query = contextRef !== undefined && contextRef !== '' ? `?contextRef=${encodeURIComponent(contextRef)}` : ''

    setState({ isLoading: true })
    fetch(`${CHANGE_SET_API}/${encodeURIComponent(changeSetId)}/review${query}`, {
      headers: { Accept: 'application/json' },
      credentials: 'same-origin'
    })
      .then(async (response) => {
        if (!response.ok) throw new Error(`review request failed: ${response.status}`)
        return await response.json()
      })
      .then((data: ChangeSetReviewData) => { if (!cancelled) setState({ data, isLoading: false }) })
      .catch((error) => { if (!cancelled) setState({ isLoading: false, error }) })

    return () => { cancelled = true }
  }, [changeSetId, contextRef])

  return state
}
