# Static Text

Adds the configured static text to the query.

## Configuration

![static_text_config.png](../../../img/graphql/static_text_config.png)

- **Text**: The text to add to the query.

## Example

![static_text_example.png](../../../img/graphql/static_text_example.png)

Request:
```graphql
{
  getCar(id: 82) {
    id,
    StaticTextForQuery
  }
}
```

Result:
```graphql
{
  "data": {
    "getCar": {
      "id": "81",
      "StaticTextForQuery": "StaticTextForQuery"
    }
  }
}
```