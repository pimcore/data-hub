## Filter Listings

You can use __Pimcore's webservice filter logic__
as described [here](https://pimcore.com/docs/5.x/Development_Documentation/Web_Services/Query_Filters.html).

Deeplink: https://demo.pimcore.fun/admin/login/deeplink?object_3_folder

![Filtered Grid](../img/graphql/filtering.png)

### Request

Get all `Manufacturer` objects which have 'Lorem ipsum' in their title field. 

```graphql
{
  getManufacturerListing(filter: "{\"name\": {\"$like\" :\"%ca%\"}}") {
    edges {
      node {
        id
        name
      }
    },
    totalCount    
  }
}
```

### Response

```
{
  "data": {
    "getManufacturerListing": {
      "edges": [
        {
          "node": {
            "id": "45",
            "name": "Cadillac"
          }
        },
        {
          "node": {
            "id": "80",
            "name": "AC Cars"
          }
        },
        {
          "node": {
            "id": "153",
            "name": "MG Cars"
          }
        }
      ],
      "totalCount": 3
    }
  }
}
```


