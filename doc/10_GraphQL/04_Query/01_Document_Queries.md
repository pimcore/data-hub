# Document Queries

## Supported Document Types

* Email
* Hardlink
* Link
* Page
* Snippet

## Supported Page Element Types

* Areablock
* Checkbox
* Date
* Embed  
* Image
* Input
* Link
* Multiselect
* Numeric
* Pdf
* Relation
* Relations
* Scheduled Block
* Select
* Table
* Textarea
* Video
* Wysiwyg
* ...

## Document Query Samples

### Fetch Document Page and Get Date Editable

```graphql
{
  getDocument(id: 25) {
    ... on document_page {
      fullpath
      editables {
        ...on document_editableDate {
          _editableName
          # unix timestamp
          timestamp
          # as formatted string
          formatted(format:"Y-m-d")
        }
      }      
    }
  }
}
```


### Fetch Document Page and Get All Editables, Including the Inherited Editables

```graphql
{
  getDocument(id: 207) {    
    ... on document_page {
      id,
      editables(getInheritedValues: true ){
        __typename
      }
    }
  }
}
```

### Fetch Document Page and get processed Wysiwyg editable content

* Field `text` contains the HTML as it is stored in Pimcore for this Wysiwyg editable.
* Field `frontend` contain the processed HTML where Pimcore Element links are rewritten where necessary.

```graphql
{
  getDocument(id: 207) {    
    ... on document_page {
      id,
      editables(getInheritedValues: true){
        __typename
        ...on document_editableWysiwyg {
          text
          frontend
        }
      }
    }
  }
}
```

### Fetch Full Rendered Document Page

The `rendered` field can be used to retrieve a rendered version of the page. Available options:
* `attributes`: Attributes passed into the controller/action
* `query`: Query Params passed into the controller/action
* `options`: Options passed into the renderer
* `use_layout`: Enable/disable Layout Rendering

```graphql
{
  getDocument(id: 207) {    
    ... on document_page {
      id,
      rendered(
        attributes: [{key: "myControllerAttributeName", value: "Hello World!"}],
        use_layout: true,
        options: [{key: "ignore_errors", value: "1"}]
      )
    }
  }
}
```

## Fetch Document Page via Data Object Relation and Get More Editable Data

* get data object ID 61
* get many-to-one relation
* get target page document
* get link editable
* get referenced page document / or news object
* get input editables of referenced page document / or shorttext of news object

```graphql
{
  getTest3(id: 61) {
    manytoone {
      ... on document_page {
        fullpath
        editables {
          ... on document_editableLink {
            _editableType
            _editableName
            data {
              internal
              path
              target {
                __typename
                ... on document_page {
                  id
                  fullpath
                  editables {
                    ... on document_editableInput {
                      name
                      text
                    }
                  }
                }
                ... on object_news {
                  shortText
                }
              }
            }
          }
        }
      }
    }
  }
}
```

### Fetch Link Document

Get Link Document and resolve the target news text.

```graphql
{
  getDocument(id: 76) {
    ... on document_link {
      fullpath
      object {
        ...on object_news {
          shortText
        }
      }      
    }
  }
}

```

## Fetch Document Properties

see [Element Properties](./11_Query_Samples/05_Sample_Element_Properties.md)

## Fetch Document Translation Links

```graphql
{
  getDocument(id: 76) {
    ... on document_page {
      id
      translations {
        id
        language
        target {
          ... on document_headlessdocument {
            fullpath
          }
        }
      }
    }
  }
}

```

### Fetch Language Specific Document Translation Link

```graphql
{
  getDocument(id: 76) {
    ... on document_page {
      id
      translations(defaultLanguage: "de") {
        ...
      }
    }
  }
}

```