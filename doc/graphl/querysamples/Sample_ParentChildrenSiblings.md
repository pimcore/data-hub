## Get objects Parent/Children/Siblings

For example, to get `berlina` object's (id:261) parent, children and siblings 

![](../../img/graphql/sample_parentChildrenSiblings.png)

### Request

```graphql
{
  getCar(id: 261) {
    id
    name
    
    parent {
      ... on object_Car {
        id
        name
      }
    }
    
    children {
      ... on object_Car {
        id
        name
        color
      }
    }
    
    _siblings {
      ... on object_Car {
        id
        name
      }
    }
  }
}
```

### Response

```graphql
{
  "data": {
    "getCar": {
      "id": "261",
      "name": "1900",
      "parent": {
        "id": "260",
        "name": "1900"
      },
      "children": [
        {
          "id": "263",
          "name": "1900",
          "color": [
            "black"
          ]
        },
        {
          "id": "262",
          "name": "1900",
          "color": [
            "silver"
          ]
        }
      ],
      "_siblings": [
        {
          "id": "264",
          "name": "1900 Sprint"
        }
      ]
    }
  }
}
```


