
## Document Queries

> Experimental + not feature complete! Subject to change without notice.

## Supported Document types

* Email
* Hardlink
* Link
* Page
* Snippet

## Supported Page Element types

* Areablock
* Checkbox
* Date
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

## Sample 1 (Link Document)

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

## Sample 2 (Page Document Editables)

* get object 61
* get manytoone relation
* get target page document
* get link tag
* get referenced page document / or news object
* get input editables of referenced page document / or shorttext of news object

```graphql
{
  getTest3(id: 61) {
    manytoone {
      ... on document_page {
        fullpath
        editables {
          ... on document_tagLink {
            _tagType
            _tagName
            data {
              internal
              path
              target {
                __typename
                ... on document_page {
                  id
                  fullpath
                  editables {
                    ... on document_tagInput {
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

## Sample 3 (Date Element Tag)

```graphql
{
  getDocument(id: 25) {
    ... on document_page {
      fullpath
      editables {
        ...on document_tagDate {
          _tagName
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

## Sample 4 (Document Properties)

see [Element Properties](./querysamples/Sample_ElementProperties.md)
