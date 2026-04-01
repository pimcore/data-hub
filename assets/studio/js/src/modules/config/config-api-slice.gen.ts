import { api } from "@pimcore/studio-ui-bundle/api";
export const addTagTypes = ["Bundle Data Hub"] as const;
const injectedRtkApi = api
    .enhanceEndpoints({
        addTagTypes,
    })
    .injectEndpoints({
        endpoints: (build) => ({
            bundleDataHubConfigAdd: build.mutation<
                BundleDataHubConfigAddApiResponse,
                BundleDataHubConfigAddApiArg
            >({
                query: (queryArg) => ({
                    url: `/pimcore-studio/api/bundle/data-hub/config/add`,
                    method: "POST",
                    params: {
                        name: queryArg.name,
                        type: queryArg["type"],
                        path: queryArg.path,
                    },
                }),
                invalidatesTags: ["Bundle Data Hub"],
            }),
            bundleDataHubConfigClone: build.mutation<
                BundleDataHubConfigCloneApiResponse,
                BundleDataHubConfigCloneApiArg
            >({
                query: (queryArg) => ({
                    url: `/pimcore-studio/api/bundle/data-hub/config/clone`,
                    method: "POST",
                    params: {
                        name: queryArg.name,
                        originalName: queryArg.originalName,
                    },
                }),
                invalidatesTags: ["Bundle Data Hub"],
            }),
            bundleDataHubConfigCollection: build.query<
                BundleDataHubConfigCollectionApiResponse,
                BundleDataHubConfigCollectionApiArg
            >({
                query: () => ({
                    url: `/pimcore-studio/api/bundle/data-hub/config`,
                }),
                providesTags: ["Bundle Data Hub"],
            }),
            bundleDataHubConfigDelete: build.mutation<
                BundleDataHubConfigDeleteApiResponse,
                BundleDataHubConfigDeleteApiArg
            >({
                query: (queryArg) => ({
                    url: `/pimcore-studio/api/bundle/data-hub/config/delete/${queryArg.name}`,
                    method: "DELETE",
                }),
                invalidatesTags: ["Bundle Data Hub"],
            }),
            bundleDataHubConfigExport: build.query<
                BundleDataHubConfigExportApiResponse,
                BundleDataHubConfigExportApiArg
            >({
                query: (queryArg) => ({
                    url: `/pimcore-studio/api/bundle/data-hub/config/${queryArg.name}/export`,
                }),
                providesTags: ["Bundle Data Hub"],
            }),
            bundleDataHubConfigGet: build.query<
                BundleDataHubConfigGetApiResponse,
                BundleDataHubConfigGetApiArg
            >({
                query: (queryArg) => ({
                    url: `/pimcore-studio/api/bundle/data-hub/config/${queryArg.name}`,
                }),
                providesTags: ["Bundle Data Hub"],
            }),
            bundleDataHubConfigUpdate: build.mutation<
                BundleDataHubConfigUpdateApiResponse,
                BundleDataHubConfigUpdateApiArg
            >({
                query: (queryArg) => ({
                    url: `/pimcore-studio/api/bundle/data-hub/config/${queryArg.name}`,
                    method: "PUT",
                    body: queryArg.bundleDataHubUpdateConfiguration,
                }),
                invalidatesTags: ["Bundle Data Hub"],
            }),
            bundleDataHubConfigImport: build.mutation<
                BundleDataHubConfigImportApiResponse,
                BundleDataHubConfigImportApiArg
            >({
                query: (queryArg) => ({
                    url: `/pimcore-studio/api/bundle/data-hub/config/import`,
                    method: "POST",
                    body: queryArg.body,
                }),
                invalidatesTags: ["Bundle Data Hub"],
            }),
        }),
        overrideExisting: false,
    });
export { injectedRtkApi as api };
export type BundleDataHubConfigAddApiResponse =
    /** status 201 bundle_data_hub_config_add_success_response */ void;
export type BundleDataHubConfigAddApiArg = {
    /** The name of the configuration */
    name: string;
    /** Type of the adapter */
    type: string;
    /** Configuration path */
    path?: string;
};
export type BundleDataHubConfigCloneApiResponse =
    /** status 201 bundle_data_hub_config_clone_success_response */ void;
export type BundleDataHubConfigCloneApiArg = {
    /** The name of the new configuration */
    name: string;
    /** The name of the configuration to clone */
    originalName: string;
};
export type BundleDataHubConfigCollectionApiResponse =
    /** status 200 bundle_copilot_actions_success_response */ {
        totalItems: number;
        items: BundleDataHubConfiguration[];
    };
export type BundleDataHubConfigCollectionApiArg = void;
export type BundleDataHubConfigDeleteApiResponse =
    /** status 200 bundle_data_hub_config_delete_success_response */ void;
export type BundleDataHubConfigDeleteApiArg = {
    /** Name of the configuration */
    name: string;
};
export type BundleDataHubConfigExportApiResponse =
    /** status 200 bundle_data_hub_config_export_success_response */ void;
export type BundleDataHubConfigExportApiArg = {
    /** Name of the configuration */
    name: string;
};
export type BundleDataHubConfigGetApiResponse =
    /** status 200 bundle_data_hub_config_get_success_response */ BundleDataHubConfigurationDetail;
export type BundleDataHubConfigGetApiArg = {
    /** Name of the configuration */
    name: string;
};
export type BundleDataHubConfigUpdateApiResponse =
    /** status 200 bundle_data_hub_config_update_success_response */ BundleDataHubUpdateConfigurationResponse;
export type BundleDataHubConfigUpdateApiArg = {
    /** Name of the configuration */
    name: string;
    bundleDataHubUpdateConfiguration: BundleDataHubUpdateConfiguration;
};
export type BundleDataHubConfigImportApiResponse =
    /** status 201 bundle_data_hub_config_import_success_response */ void;
export type BundleDataHubConfigImportApiArg = {
    /** Configuration file to import */
    body: {
        /** JSON configuration file */
        file: Blob;
    };
};
export type Error = {
    /** Message */
    message: string;
};
export type DevError = {
    /** Message */
    message: string;
    /** Details */
    details: string;
};
export type BundleDataHubConfiguration = {
    /** AdditionalAttributes */
    additionalAttributes?: {
        [key: string]: string | number | boolean | object;
    };
    /** ID */
    id: string;
    /** Text */
    text: string;
    /** Type */
    type: string;
    /** iconCls */
    iconCls: string;
    /** Expandable */
    expandable: boolean;
    /** Leaf */
    leaf: boolean;
    /** Permissions */
    permissions: any;
    /** Allow children */
    allowChildren?: boolean;
    /** Group */
    group?: any;
    /** Children */
    children?: BundleDataHubConfiguration[];
    /** Adapter */
    adapter: any;
    /** Writable */
    writable: boolean;
    /** Has Studio Column Configuration */
    studioColumnConfig: boolean;
};
export type BundleDataHubConfigurationDetail = {
    /** AdditionalAttributes */
    additionalAttributes?: {
        [key: string]: string | number | boolean | object;
    };
    /** Configuration name */
    name: string;
    /** Configuration data */
    configuration: object;
    /** User permissions */
    userPermissions: object;
    /** Supported GraphQL query data types */
    supportedGraphQLQueryDataTypes: string[];
    /** Supported GraphQL mutation data types */
    supportedGraphQLMutationDataTypes: string[];
    /** Modification date timestamp */
    modificationDate: number;
};
export type BundleDataHubUpdateConfigurationResponse = {
    /** New modification date timestamp */
    modificationDate: number;
};
export type BundleDataHubUpdateConfiguration = {
    /** JSON-encoded configuration containing general, schema, security, workspaces, and permissions */
    data: string;
    /** Client-side modification date timestamp for conflict detection */
    modificationDate: number;
};
export const {
    useBundleDataHubConfigAddMutation,
    useBundleDataHubConfigCloneMutation,
    useBundleDataHubConfigCollectionQuery,
    useBundleDataHubConfigDeleteMutation,
    useBundleDataHubConfigExportQuery,
    useBundleDataHubConfigGetQuery,
    useBundleDataHubConfigUpdateMutation,
    useBundleDataHubConfigImportMutation,
} = injectedRtkApi;
