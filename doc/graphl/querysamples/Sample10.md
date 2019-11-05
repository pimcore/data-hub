## Get Element Properties


![Sample Document Properties](../../img/graphql/element_properties.png)

### Request

Properties can be filtered by providing the `keys` argument. All properties will
be returned by default.

```graphql
{
  getDocument(id: 50) {
    ... on document_page {
      fullpath
      properties(keys: ["leftNavStartNode", "blog", "mytext", "myobject", "mylogo"]) {
        __typename
        ... on property_text {
          name
          text
        }
        ... on property_document {
          name
          type
          document {
            __typename
            ... on document_page {
              id
              fullpath
            }
          }
        }
        ... on property_asset {
          name
          asset {
            fullpath
            logothumb: fullpath(thumbnail: "content")
          }
        }
        ... on property_object {
          name
          object {
            ... on object_news {
              id
              title(language: "en_GB")
              date
            }
          }
        }
      }
    }
  }
}
```

### Response

```graphql
{
  "data": {
    "getDocument": {
      "fullpath": "/de/einfuehrung",
      "properties": [
        {
          "__typename": "property_document",
          "name": "blog",
          "type": "document",
          "document": {
            "__typename": "document_page",
            "id": "60",
            "fullpath": "/en/advanced-examples/blog"
          }
        },
        {
          "__typename": "property_document",
          "name": "leftNavStartNode",
          "type": "document",
          "document": {
            "__typename": "document_page",
            "id": "41",
            "fullpath": "/de"
          }
        },
        {
          "__typename": "property_text",
          "name": "mytext",
          "text": "some text"
        },
        {
          "__typename": "property_object",
          "name": "myobject",
          "object": {
            "id": "3",
            "title": "Lorem Ipsum",
            "date": "2013-07-18 13:45:00"
          }
        },
        {
          "__typename": "property_asset",
          "name": "mylogo",
          "asset": {
            "fullpath": "/examples/italy/dsc04344.jpg",
            "logothumb": "/examples/italy/image-thumb__39__content/dsc04344.webp"
          }
        }
      ]
    }
  }
}
```
