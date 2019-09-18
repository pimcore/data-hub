
## Document Queries

> Experimental + not feature complete! Subject to change without notice.

## Supported Document types

* Email
* Hardlink
* Link
* Page

## Supported Page Element types

* Input
* Link
* Numeric
* Select
* Textarea
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
            type
            name
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

