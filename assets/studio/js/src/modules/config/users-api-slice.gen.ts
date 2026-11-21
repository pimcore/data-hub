import { api } from "@pimcore/studio-ui-bundle/api";
export const addTagTypes = ["Bundle Data Hub"] as const;
const injectedRtkApi = api
    .enhanceEndpoints({
        addTagTypes,
    })
    .injectEndpoints({
        endpoints: (build) => ({
            bundleDataHubUsersCollection: build.query<
                BundleDataHubUsersCollectionApiResponse,
                BundleDataHubUsersCollectionApiArg
            >({
                query: (queryArg) => ({
                    url: `/pimcore-studio/api/bundle/data-hub/users`,
                    params: { type: queryArg["type"] },
                }),
                providesTags: ["Bundle Data Hub"],
            }),
        }),
        overrideExisting: false,
    });
export { injectedRtkApi as api };
export type BundleDataHubUsersCollectionApiResponse =
    /** status 200 bundle_data_hub_users_collection_success_response */ {
        totalItems: number;
        items: BundleDataHubPermissionUser[];
    };
export type BundleDataHubUsersCollectionApiArg = {
    /** Filter by user type (user or role) */
    type?: "user" | "role";
};
export type BundleDataHubPermissionUser = {
    /** AdditionalAttributes */
    additionalAttributes?: {
        [key: string]: string | number | boolean | object;
    };
    /** User or Role ID */
    id: number;
    /** User or Role name */
    text: string;
    /** Element type */
    elementType: string;
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
export const { useBundleDataHubUsersCollectionQuery } = injectedRtkApi;
