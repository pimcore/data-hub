# Using Aliases

Aliases are used to rename the result of a field. 
This is useful when you want to return multiple fields with the same name.

There are two ways to use aliases:

## Alias Operator

See [Alias operator](../08_Operators/Query/Alias.md) for more information.

## Alias in the Query

Get a `Car` with id 82 and return the number of doors.
The first value is the alias, the second value is the field name.

#### Request
```graphql
{
  getCar(id: 82) {  
  doors: numberOfDoors
  }
}
```

#### Response
```json
{
    "data": {
        "getCar": {
            "doors": 2
        }
    }
}
```


