# Translate Value

Translates the values of the selected fields. Add this operator to the list and drag & drop the desired fields into the operator.
For translation the default locale is used.

Similar to Pimcore's [Translate Value](https://pimcore.com/docs/6.x/User_Documentation/DataObjects/Grid_Configuration_Operators/Operators/TranslateValue.html). For a detailed example see [Website Translations](./11_Query_Samples/27_Sample_Translate_Values.md).

## Configuration

- **Label**: The label of the field.
- **Prefix**: The prefix for the translation key.

## Example

Request:
```graphql
{
  getCar(id: 28) {
    id,
    name,
    translatedname
  }
}
```
