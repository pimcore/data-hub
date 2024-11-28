# [FreeForm] Create a New Link Document

```graphql
mutation {
  createDocumentLink(key: "newlinkdocument", parentId:1, input: {
    internal:308
    internalType:"asset"         
    }    
  
  ) {
    success
  }
}
```

<div class="image-as-lightbox"></div>

![Grid](../../../img/graphql/document_create_link.png)
