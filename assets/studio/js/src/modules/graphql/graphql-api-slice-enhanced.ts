/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { api as baseApi } from './graphql-api-slice.gen'

const enhancedGraphqlApiSlice = baseApi.enhanceEndpoints({
  addTagTypes: ['DataHubGraphql'],
  endpoints: {
    bundleDataHubGraphqlExplorer: {
      providesTags: (result, error, arg) => [
        { type: 'DataHubGraphql', id: arg.clientname }
      ]
    }
  }
})

export { enhancedGraphqlApiSlice as api }

export const {
  useBundleDataHubGraphqlExplorerQuery,
  useLazyBundleDataHubGraphqlExplorerQuery,
  useBundleDataHubGraphqlExplorerUrlQuery,
  useLazyBundleDataHubGraphqlExplorerUrlQuery
} = enhancedGraphqlApiSlice

export type * from './graphql-api-slice.gen'
