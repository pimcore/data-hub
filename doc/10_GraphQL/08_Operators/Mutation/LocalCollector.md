# Locale Collector

Allows editing all languages for a single field.

## Configuration

- **Label**: Name for the field to be used in the mutation.

## Example

Request:
```graphql
mutation {
  updateCar(id: 28, data: {
    name: {
      de: "Auto",
      en: "Car",
      fr: "Voiture"
    }
  }) {
    id,
    name
  }
}
```