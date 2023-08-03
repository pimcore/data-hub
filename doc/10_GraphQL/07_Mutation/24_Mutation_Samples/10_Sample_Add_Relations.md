# Sample Add Relations
This will add relations to categories relation field of Car object. Type can be omitted for 
mutations only allowing one type, e.g. many-to-many-objects .

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