## Mutations

>TODO: Align this with the new demo as soon as reasonable content is available.
 
* Create Objects
* Update Objects
* Delete Objects

In addition, you can turn on mutations to modify assets, create object folders and on on.
 

![Mutation grid](../img/graphql/mutation_grid.png)
 
Note that for `Create` and `Update` operate you can query the updated data using the same request.
Keep in mind that for all kinds of mutations you need the `Write` permission.

## Supported mutation datatypes

![Mutation grid](../img/graphql/mutation_grid.png)

Also check out the Pimcore's [data type documentation](https://pimcore.com/docs/6.x/Development_Documentation/Objects/Object_Classes/Data_Types/index.html). 

* Boolean Select
* Checkbox
* Country
* Countries (Multiselect)
* Date
* DateTime
* Email
* External Image
* Firstname
* Gender
* Geopoint
* Image
* Input
* Language
* Lastname
* Many-to-One Relation
* Many-to-Many Relation
* Many-to-Many Object Relation
* Multiselect
* Newsletter Active
* Newsletter Confirmed
* Numeric
* Select
* Slider
* Textarea
* Time
* Wysiwyg

## Supported mutation operators

* IfEmpty
* Locale Switcher
* ...

See [the overview page](./MutationOperators.md) for more details.

## Create Object

TBD: do we need an extra workspace permission for that ? (in addition to write which would be only used for updates)

Request:
```
mutation {
  createNews(parentId: 66, key: "testcreate27", published: false) {
    success
    message
    output(defaultLanguage: "de") {
      id      
      creationDate
      fullpath
      title(language: "en")
    }
  }
}
```

Response:
```
{
  "data": {
    "createNews": {
      "success": true,
      "message": "object created: 98",
      "output": {
        "id": "98",
        "creationDate": 1559642310,
        "fullpath": "/tests/testcreate2/testcreate27",
        "title": null
      }
    }
  }
}
```

## Update Object

Updates german title and short text and returns the modification date. 

Request:
```
mutation {
  updateNews(id: 8, defaultLanguage: "de", input: {title: "german TITLE", shortText: "new short text"}) {
    success
    output {
      modificationDate
    }
  }
}
```

Response:
```
{
  "data": {
    "updateNews": {
      "success": true,
      "output": {
        "modificationDate": 1559746654
      }
    }
  }
}
```

## Delete Object

Request:
```
mutation {
  deleteBlogCategory(id: 37) {
    success
    message
  }
}
```

Response:
```
{
  "data": {
    "deleteBlogCategory": {
      "success": true,
      "message": ""
    }
  }
}
```

## Update Asset

This will rename the asset + update the data.
Request:
```
mutation {
  updateAsset(id: 76, input: {filename:"newfilename",
  data:"iVBORw0KGgoAAAANSUhEUg....."}) {
    success
    message
    assetData {
         modificationDate
    }   
  }
}
```

## Add Relations
This will add relations to categories relation field of Car object. Type can be omitted for mutations only allowing one type, e.g. many-to-many-objects 

> Note: Read permissions are required for related objects to be assigned.

Request:
```
mutation {
  createCar (
    key: "test-car",
    parentId: 1086,
    published: true,
    input: {
      name: "Test Car",
      categories: [
        {id: 390, type: "object"},
        {id: 392, type: "document"}
      ]
    }
  ) {
        output(defaultLanguage: "en") {
          id      
          creationDate
          fullpath
        }
    }
}
```

## How to extend it

* [Add a custom mutation datatype](./AddCustomMutationDatatype.md)
* [Add a custom mutation operator](./AddCustomMutationOperator.md)
