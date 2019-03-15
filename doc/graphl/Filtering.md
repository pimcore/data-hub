## Filter Listings

You can use __Pimcore's webservice filter logic__
as described [here](https://pimcore.com/docs/5.x/Development_Documentation/Web_Services/Query_Filters.html).

### Request

Get all news objects which have 'Lorem ipsum' in their title field. 

```
{
  getNewsListing(filter: "{\"title\": {\"$like\" :\"%Lorem ipsum%\"}}") {
    edges {
      node {
        id
        fullpath
        title
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
    "getNewsListing": {
      "edges": [
        {
          "node": {
            "id": "3",
            "fullpath": "/news4/lorem-ipsum",
            "title": "Lorem ipsum dolor sit amet"
          }
        }
      ],
      "totalCount": 1
    }
  }
}
```


