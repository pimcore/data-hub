# [TreeAPI] Create Document With Areablocks and Nested Block With Images 

Note that this produces the same result as [this example](./01_FreeformAPI_Create_Document_with_Areablocks.md) 
but uses the nested API instead of the free-form approach

```graphql
mutation {
   createDocumentPage(
      key: "documentkey48"
      parentId: 1
      input: {
         editables: {
            areablock: [
               {
                  _editableName: "content"
                  items: [
                     {
                        type: "headlines"
                        hidden: false # optional
                        replace: false # defaults to true, all editables will be replaced (of course, this only makes sense for updates)
                        editables: {
                           input: [
                              {
                                 _editableName: "headline"
                                 text: "HEY, I AM A SUBHEADLINE"
                              }
                           ]
                           wysiwyg: [
                              { _editableName: "lead", text: "The lead text" }
                           ]
                        }
                     }
                     {
                        type: "wysiwyg-with-images"
                        replace: false
                        editables: {
                           block: [
                              {
                                 _editableName: "images"
                                 items: [
                                    {
                                       replace: false # replace all elements inside the editable
                                       editables: {
                                          image: [
                                             {
                                                _editableName: "image"
                                                alt: "alt text for image 1"
                                                id: 18
                                             }
                                          ]
                                       }
                                    }
                                    {
                                       replace: true
                                       editables: {
                                          image: [
                                             {
                                                _editableName: "image"
                                                alt: "alt text for image 2"
                                                id: 22
                                             }
                                          ]
                                       }
                                    }
                                 ]
                              }
                           ]
                           wysiwyg: [
                              {
                                 _editableName: "content"
                                 text: "<b>Lorem Ipsum</b> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book..."
                              }
                           ]
                        }
                     }
                     {
                        type: "image"
                        editables: {
                           image: [
                              { _editableName: "image", alt: "alt text", id: 67 }
                           ]
                        }
                     }
                  ]
               }
            ]
            input: [{ _editableName: "headline", text: "THIS IS THE HEADLINE" }]
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