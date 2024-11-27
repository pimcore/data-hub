#  Element Counter

Counts the elements assigned to the selected field.
Useful for counting the number of elements in a relation field.

## Configuration

![element_counter_config.png](../../../img/graphql/element_counter_config.png)

- **Label**: Name for the field to use in the query.
- **Count Empty**: If checked, the operator will also count empty fields.

## Example

![element_counter_example.png](../../../img/graphql/element_counter_example.png)

Request:
```
{
  getCar(id: 82) {
    id,
    CategoriesCount
  }
}
```

Response:
```json
{
    "data": {
        "getCar": {
            "id": "82",
            "CategoriesCount": 2
        }
    }
}
```