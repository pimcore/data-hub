/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { api as baseApi } from './config-api-slice.gen'

export const api = baseApi
  .enhanceEndpoints({
    addTagTypes: ['DataHubConfigs'],
    endpoints: {
      bundleDataHubConfigCollection: {
        providesTags: ['DataHubConfigs']
      },
      bundleDataHubConfigAdd: {
        invalidatesTags: ['DataHubConfigs']
      },
      bundleDataHubConfigClone: {
        invalidatesTags: ['DataHubConfigs']
      },
      bundleDataHubConfigDelete: {
        invalidatesTags: ['DataHubConfigs']
      },
      bundleDataHubConfigGet: {
        providesTags: []
      },
      bundleDataHubConfigExport: {
        providesTags: (result, error, arg) => [{ type: 'DataHubConfigs', id: arg.name }]
      },
      bundleDataHubConfigImport: {
        invalidatesTags: ['DataHubConfigs']
      },
      bundleDataHubConfigUpdate: {
        invalidatesTags: ['DataHubConfigs']
      }
    }
  })

export type * from './config-api-slice.gen'

export const {
  useBundleDataHubConfigCollectionQuery,
  useBundleDataHubConfigAddMutation,
  useBundleDataHubConfigCloneMutation,
  useBundleDataHubConfigDeleteMutation,
  useBundleDataHubConfigGetQuery,
  useBundleDataHubConfigExportQuery,
  useBundleDataHubConfigImportMutation,
  useBundleDataHubConfigUpdateMutation
} = api
