/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useEffect, useMemo, useState } from 'react'
import { useAppDispatch } from '@pimcore/studio-ui-bundle/app'
import { api } from '@pimcore/studio-ui-bundle/api/data-object'
import { type StoreKeyLabel, storeKeyId } from './column-identity'

/** One Classification Store field whose configured keys need labelling */
export interface StoreFieldRef {
  fieldName: string
  storeId: number
}

/** A whole store in one trip for any store this demo-sized configuration addresses */
const PAGE_SIZE = 200

/** Guard against a paging answer that never terminates */
const MAX_PAGES = 25

interface RelationItem {
  keyId: number
  groupId: number
  keyName: string
  groupName: string
  /** Read untyped: the member ships ahead of the generated client */
  keyTitle?: string | null
}

interface RelationPage {
  totalItems?: number
  items?: RelationItem[]
}

/**
 * The endpoint is reached through the shared api object rather than a generated hook: the SDK barrel
 * exports no classification-store slice, and widening it would need a studio-ui canary. Absent at
 * runtime it simply yields no labels, and the rows fall back to the key identifier.
 */
const relationsEndpoint = (api.endpoints as Record<string, any>).classificationStoreGetKeyGroupRelations

/**
 * The labels of every Classification Store key the given store fields offer, keyed by
 * {@see storeKeyId}. Group ids are unique across stores, so the fields share one map.
 *
 * Fetched once per store field rather than once per configured key: a configuration naming ten keys
 * of one store would otherwise cost ten requests, and the page the first one returns already holds
 * all ten.
 */
export const useStoreKeyLabels = (
  { classId, storeFields }: { classId: string, storeFields: StoreFieldRef[] }
): ReadonlyMap<string, StoreKeyLabel> => {
  const dispatch = useAppDispatch()
  const [labels, setLabels] = useState<ReadonlyMap<string, StoreKeyLabel>>(new Map())

  // Stable serialization as the effect dependency: the caller rebuilds the array on every render
  const fieldsKey = useMemo(
    () => storeFields.map(field => `${field.storeId}:${field.fieldName}`).sort((a, b) => a.localeCompare(b)).join(','),
    [storeFields]
  )

  useEffect(() => {
    if (fieldsKey === '' || classId === '' || relationsEndpoint === undefined) {
      // the same empty map back, so nothing re-renders over an answer there was never anything to give
      setLabels(prev => prev.size === 0 ? prev : new Map())
      return
    }

    let cancelled = false

    const fetchPage = async (field: StoreFieldRef, page: number): Promise<RelationPage> => {
      const result = await dispatch(relationsEndpoint.initiate({
        storeId: field.storeId,
        classId,
        fieldName: field.fieldName,
        page,
        pageSize: PAGE_SIZE
      }))

      return (result.data ?? {}) as RelationPage
    }

    const fetchField = async (field: StoreFieldRef): Promise<RelationItem[]> => {
      const first = await fetchPage(field, 1)
      const items = [...(first.items ?? [])]
      const totalItems = first.totalItems ?? items.length

      for (let page = 2; page <= MAX_PAGES && items.length < totalItems; page++) {
        const next = await fetchPage(field, page)
        if ((next.items ?? []).length === 0) break
        items.push(...(next.items ?? []))
      }

      return items
    }

    void Promise.all(storeFields.map(fetchField)).then((pages) => {
      if (cancelled) return

      const resolved = new Map<string, StoreKeyLabel>()
      pages.forEach((items) => {
        items.forEach((item) => {
          resolved.set(storeKeyId(item.groupId, item.keyId), {
            keyName: item.keyName,
            groupName: item.groupName,
            keyTitle: item.keyTitle
          })
        })
      })

      // nothing to give and nothing given yet is not a change; re-rendering the editor over it is waste
      setLabels(prev => prev.size === 0 && resolved.size === 0 ? prev : resolved)
    })

    return () => {
      cancelled = true
    }
    // storeFields itself is intentionally not a dependency; fieldsKey stands for its content
  }, [fieldsKey, classId, dispatch])

  return labels
}
