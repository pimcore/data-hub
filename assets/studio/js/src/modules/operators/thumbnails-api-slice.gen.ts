import { api } from "@pimcore/studio-ui-bundle/api";
export const addTagTypes = ["Bundle Data Hub"] as const;
const injectedRtkApi = api
    .enhanceEndpoints({
        addTagTypes,
    })
    .injectEndpoints({
        endpoints: (build) => ({
            bundleDataHubThumbnailsCollection: build.query<
                BundleDataHubThumbnailsCollectionApiResponse,
                BundleDataHubThumbnailsCollectionApiArg
            >({
                query: () => ({
                    url: `/pimcore-studio/api/bundle/data-hub/thumbnails`,
                }),
                providesTags: ["Bundle Data Hub"],
            }),
        }),
        overrideExisting: false,
    });
export { injectedRtkApi as api };
export type BundleDataHubThumbnailsCollectionApiResponse =
    /** status 200 bundle_data_hub_thumbnails_collection_success_response */ {
        totalItems: number;
        items: BundleDataHubThumbnail[];
    };
export type BundleDataHubThumbnailsCollectionApiArg = void;
export type BundleDataHubThumbnail = {
    /** AdditionalAttributes */
    additionalAttributes?: {
        [key: string]: string | number | boolean | object;
    };
    /** Thumbnail ID */
    id: string;
    /** Thumbnail name */
    text: string;
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
export const { useBundleDataHubThumbnailsCollectionQuery } = injectedRtkApi;
