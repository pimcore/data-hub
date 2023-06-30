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
![Grid](../../../img/graphql/document_create_link.png)
