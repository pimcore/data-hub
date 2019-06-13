## Get News Listing

### Request

News listing with limit 3 and offset 1
```
{
  # 'first' is the limit
  # 'after' the offset
  getNewsListing(first: 3, after: 1, defaultLanguage: "de") {
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
            "id": "4",
            "fullpath": "/news/in-enim-justo",
            "title": "Li Europan lingues es membres"
          }
        },
        {
          "node": {
            "id": "5",
            "fullpath": "/news/nam-eget-dui",
            "title": "Zwei flinke Boxer jagen die quirlige Eva"
          }
        },
        {
          "node": {
            "id": "6",
            "fullpath": "/news/in-enim-justo_2",
            "title": "Li Europan lingues es membres"
          }
        }
      ],
      "totalCount": 7
    }
  }
}
```


