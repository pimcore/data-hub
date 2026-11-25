/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

type QueryResult = Record<string, unknown>
type Handler = (arg: any) => QueryResult

/** Controllable stand-in for the RTK Query endpoints the schema modal uses. */
export const apiState: {
  available: QueryResult
  grid: Handler
  gridPreview: Handler
  /** The key-group-relations page for one request; answers `{ data: { totalItems, items } }` */
  keyGroupRelations: Handler
  calls: { grid: any[], gridPreview: any[], available: any[], keyGroupRelations: any[] }
  keyGroupRelationUnsubscribes: number
} = {
  available: {},
  grid: () => ({}),
  gridPreview: () => ({}),
  keyGroupRelations: () => ({}),
  calls: { grid: [], gridPreview: [], available: [], keyGroupRelations: [] },
  keyGroupRelationUnsubscribes: 0
}

export const resetApiState = (): void => {
  apiState.available = { data: { columns: [] }, isLoading: false }
  apiState.grid = () => ({})
  apiState.gridPreview = () => ({})
  apiState.keyGroupRelations = () => ({ data: { totalItems: 0, items: [] } })
  apiState.calls = { grid: [], gridPreview: [], available: [], keyGroupRelations: [] }
  apiState.keyGroupRelationUnsubscribes = 0
}

export const makeEndpoint = (
  name: keyof typeof apiState.calls,
  respond: (arg: any) => QueryResult
): { useQuery: (arg: any, options?: { skip?: boolean }) => QueryResult } => ({
  // Skipped queries are not recorded and return nothing, as RTK Query does.
  useQuery: (arg: any, options?: { skip?: boolean }): QueryResult => {
    if (options?.skip === true) return {}
    apiState.calls[name].push(arg)
    const r = respond(arg)
    // Handlers that return only `data` keep their meaning: current data is the data, fetching follows loading.
    // Only a missing key is filled, so an explicit `currentData: undefined` stays.
    return {
      ...r,
      ...(!('currentData' in r) ? { currentData: r.data } : {}),
      ...(!('isFetching' in r) ? { isFetching: r.isLoading === true } : {})
    }
  }
})
