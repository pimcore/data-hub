# Get Asset Listing

### Request

Note that for the fullpath and the base64 encoded data you can specify a thumbnail config.

```
{
  getAssetListing {
    edges {
      node {
        __typename
      }
    }
  }
}

```

### Response

```
{
  "data": {
    "getAssetListing": {
      "edges": [
        {
          "node": {
            "__typename": "asset_folder"
          }
        },
        {
          "node": {
            "__typename": "asset"
          }
        },
        {
          "node": {
            "__typename": "asset"
          }
        },
        {
          "node": {
            "__typename": "asset"
          }
        },
        {
          "node": {
            "__typename": "asset"
          }
        }
      ]
    }
  }
}
```
