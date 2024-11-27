# Locale Switcher

Switches to different language other than the default language. Add the operator to the list and 

## Configuration

- **Label**: Name for the field to be displayed in the right panel.
- **Locale**: The locale you want to switch to.

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