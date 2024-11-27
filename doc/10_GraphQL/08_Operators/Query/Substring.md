# Substring

This operator extracts a substring from a string.

## Configuration

- **FieldName**: Name for the field to use in the query.
- **Start**: The position of the first character to extract.
- **Length**: The number of characters to extract.
- **Ellipses**: If the string is longer than the specified length, an ellipsis is added at the end.

## Example

Request:
```graphql
{
  getCar(id: 28) {
    id,    
    substringName
  }
}
```