import { api } from "@pimcore/studio-ui-bundle/api";
export const addTagTypes = ["Bundle Data Hub"] as const;
const injectedRtkApi = api
    .enhanceEndpoints({
        addTagTypes,
    })
    .injectEndpoints({
        endpoints: (build) => ({
            bundleDataHubGraphqlExplorer: build.query<
                BundleDataHubGraphqlExplorerApiResponse,
                BundleDataHubGraphqlExplorerApiArg
            >({
                query: (queryArg) => ({
                    url: `/pimcore-studio/api/bundle/data-hub/graphql/explorer/${queryArg.clientname}`,
                }),
                providesTags: ["Bundle Data Hub"],
            }),
            bundleDataHubGraphqlExplorerUrl: build.query<
                BundleDataHubGraphqlExplorerUrlApiResponse,
                BundleDataHubGraphqlExplorerUrlApiArg
            >({
                query: (queryArg) => ({
                    url: `/pimcore-studio/api/bundle/data-hub/graphql/explorer-url/${queryArg.name}`,
                }),
                providesTags: ["Bundle Data Hub"],
            }),
        }),
        overrideExisting: false,
    });
export { injectedRtkApi as api };
export type BundleDataHubGraphqlExplorerApiResponse =
    /** status 200 bundle_data_hub_graphql_explorer_success_response */ string;
export type BundleDataHubGraphqlExplorerApiArg = {
    /** Clientname of the client */
    clientname: string;
};
export type BundleDataHubGraphqlExplorerUrlApiResponse =
    /** status 200 bundle_data_hub_graphql_explorer_url_success_response */ {
        /** The GraphQL Explorer URL for the specified configuration */
        explorerUrl: string;
    };
export type BundleDataHubGraphqlExplorerUrlApiArg = {
    /** Name of the configuration */
    name: string;
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
export const {
    useBundleDataHubGraphqlExplorerQuery,
    useBundleDataHubGraphqlExplorerUrlQuery,
} = injectedRtkApi;
