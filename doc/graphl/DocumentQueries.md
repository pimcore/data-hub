
## Document Queries

> Experimental + not feature complete! Subject to change without notice.

## Supported Document types

* Email
* Hardlink
* Link
* Page
* Snippet

## Supported Page Element types

* Checkbox
* Date
* Image
* Input
* Link
* Numeric
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

## Sample 2 (Page Document Elements)

* get object 61
* get manytoone relation
* get target page document
* get link tag
* get referenced page document / or news object
* get input elements of referenced page document / or shorttext of news object

```graphql
{
  getTest3(id: 61) {
    manytoone {
      ... on document_page {
        fullpath
        elements {
          ... on document_tagLink {
            __tagType
            __tagName
            data {
              internal
              path
              target {
                __typename
                ... on document_page {
                  id
                  fullpath
                  elements {
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
      elements {
        ...on document_tagDate {
          __tagName
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

## Sample 4 (Properties)

see [Element Properties](./querysamples/Sample10.md)
