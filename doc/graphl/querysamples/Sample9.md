## Field-Collections on Data Objects

Data Models:

![Data Object Structure](../../img/graphql/dataobject_structure.png)

![Field Collection Structure](../../img/graphql/fieldcollections_structure.png)

### Request

```graphql
{
  getObject(id: 50) {
    myFieldCollection {
      ... on fieldcollection_fcName {
        dataName
      }
    }
  }
}
```

### Response

Here you also see the use of aliases.

```
{
  "data": {
    "getObject": {
      "myFieldCollection": [
        {
          "dataName": "Your value here"
        },
        {
          "dataName": "The second value"
        }
      ]
    }
  }
}
```
