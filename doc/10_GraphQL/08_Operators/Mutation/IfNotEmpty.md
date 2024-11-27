# IfNotEmpty

Only sets the value if current one is empty. Add the operator to the list and drag & drop the desired field into the operator.

## Configuration

- **Label**: Name for the field to be displayed in the right panel.

## Example

Request:
```graphql
{
  getCar(id: 28) {
    id,
    name,
    description    
  }
}
```

