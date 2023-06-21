# Document Mutations

## Supported Document Types

* Email
* Link
* Page
* ...

## Supported Page Element Types

* Areablock
* Block
* Embed  
* Image (for now only target asset + alt text, no hotspots or markers)
* Input
* Multiselect
* Scheduled Block
* Select
* Wysiwyg

## Mutation Modes

There a two modes for document mutations. The Free-form API allows updating single editable content but
requires lots of Pimcore insight knowledge. The Tree API provides a more intuitive nested approach for
creating and updating documents. 


### Free-form API

> Important Note: To be able to fully exploit this feature you have to understand Pimcore's [editable naming strategy](https://pimcore.com/docs/6.x/Development_Documentation/Documents/Editable_Naming_Strategies.html) 

Update or add single or multiple editables by defining their exact name and their content.

##### Sample (Update Existing Document)


```graphql
mutation {
   updateDocumentPage(
      id: 99
      input: {
         editableUpdateStrategy: replaceAll    # defaults to update
         editables: {
            input: [
               { _editableName: "content:2.headline", text: "HEYYOU 3" }
               { _editableName: "headline", text: "NEW 2" }
            ]
            wysiwyg: [
               { _editableName: "content:1.content", text: "my new <b>wysiwyg</b>" }
            ]
         }
         module: "mymodule"
      }
   ) {
      success
      document {
         controller
      }
   }
}
```

##### Additional Examples
See following list for more examples with the free-form API approach:

- [Create document with areablocks and nested block with images](./24_Mutation_Samples/01_FreeformAPI_Create_Document_with_Areablocks.md)
- [Update email document](./24_Mutation_Samples/02_FreeformAPI_Update_Email_Document.md)
- [Create a new link document](./24_Mutation_Samples/03_FreeformAPI_Create_new_Link_Document.md)


### Tree API

If you are not familiar with Pimcore's [editable naming strategy](https://pimcore.com/docs/6.x/Development_Documentation/Documents/Editable_Naming_Strategies.html)
you can also use the nested approach.

### Sample (Update a Page With an Areablock Using the Nested Approach)

```graphql
mutation {
   updateDocumentPage(
      id: 99
      input: {
         editables: {
            areablock: [
               {
                  _editableName: "content"
                  items: [
                     {
                        type: "headlines"
                        editables: {
                           input: [
                              {
                                 _editableName: "headline"
                                 text: "HEY, I AM A SUBHEADLINE"
                              }
                           ]
                        }
                     }
                  ]
               }
            ]
         }
         controller: "@AppBundle\\Controller\\ContentController"
         action: "default"
      }
   ) {
      success
      document {
         controller
         elements {
            __typename
         }
      }
   }
}
```

##### Additional Examples
See following list for more examples with the tree API approach:

- [Create document with areablocks and nested block with images](./24_Mutation_Samples/04_TreeAPI_Create_Document_with_Areablocks.md)