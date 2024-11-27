# Trimmer

Trims the value. 

## Configuration

- **Label**: Name for the field to use in the query.
- **Trim**: Where to trim, either `both`, `left`, `right` or `disabled`.

## Example

Request:
```graphql
{
  getPerson(id: 28) {
    trimmedname
  }
}
```