# Static Text

Adds the configured static text to the query.

## Configuration

- **Text**: The text to add to the query.

## Example

Request:
```graphql
{
  getCar(id: 28) {
    id,
    staticText
  }
}
```