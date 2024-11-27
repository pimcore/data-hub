# Merge (deprecated)

Merges the values of the child elements.

## Configuration

- **Label**: Name.
- **Unique**: If checked, only unique values will be returned, thus removing duplicates.

## Example

Request:
```graphql
{
  getPerson(id: 28) {
    mergedname
  }
}
```

