## Many-to-Many Object Relation

![Data](../img/graphql/many_to_many_object_relation.png)

### Request

```
{
  getUser(id: 50) {
    myobjects {
      ... on object_news {
        id,
        classname
        title
      }
    }
  }
}

```

### Response

```
{
  "data": {
    "getUser": {
      "myobjects": [
        {
          "id": "4",
          "classname": "news",
          "title": "In enim justo"
        },
        {
          "id": "3",
          "classname": "news",
          "title": "Lorem ipsum dolor sit amet"
        }
      ]
    }
  }
}
```


