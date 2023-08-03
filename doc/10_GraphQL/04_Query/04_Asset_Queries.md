# Asset Queries

Asset queries support getting single assets, single asset folders and asset listings. 

## Get Single Asset

Base structure for getting single asset: 

```graphql
{
  getAsset(id: 4) {
  ...
  }
} 
    
```

See also following examples: 
- [Sample GetAsset](./11_Query_Samples/11_Sample_GetAsset.md)
- [Sample Asset Metadata](./11_Query_Samples/12_Sample_Asset_Metadata.md)


## Get Single Asset Folder

Base structure for getting single asset folder (same as for assets): 

```graphql
{
  getAssetFolder(id: 4) {
  ...
  }
} 
    
```

## Get List of Assets

Base structure for getting a list of assets, restricted by IDs: 

```graphql
{
  getAssetListing(ids: "4,5") {
    edges {
    ...
```

See also following examples: 
- [Sample GetAssetListing](./11_Query_Samples/13_Sample_GetAssetListing.md)
 
 
#### Pagination
Pagination can be applied as query parameters.

```graphql
{
  # 'first' is the limit
  # 'after' the offset
  getAssetListing(first: 3, after: 1) {
    edges {
      ...
    }
  }
}
```


#### Simple Sorting
Sorting can be applied as query parameters, for example sort by filename, descending.

```graphql
{
  getManufacturerListing(sortBy: "filename", sortOrder: "DESC") {
    edges {
      node {
        id
        name
      }
    }
  }
}
```

#### Filtering

You can use Pimcore's webservice filter logic as described 
[here](https://pimcore.com/docs/pimcore/6.9/Development_Documentation/Web_Services/Query_Filters.html) 
for filtering listing requests.

For details see [filtering documentation page](./10_Filtering.md)


## Localization of Queries
Queries can be localized For details see the [localization documentation page](./08_Localization.md).

