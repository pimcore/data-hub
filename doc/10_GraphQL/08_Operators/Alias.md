# Alias

Simply gives the child node a different name.

If you are looking for a way to directly use aliases in a GraphQL query, please see[11_Using_Aliases.md](../04_Query/11_Using_Aliases.md) . 

## Configuration

- **Attribute**: The new name for the field.

## Example

In this example, the field `key` is renamed to `AliasForKey`. 

Request:
```graphql
{
  getCar(id: 28) {
    id,
    key,
    AliasForKey
  }
}
```



