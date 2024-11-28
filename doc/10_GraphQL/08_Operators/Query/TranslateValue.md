# Translate Value

Translates the values of the selected fields. For translation the default locale is used.

Similar to Pimcore's [Translate Value](https://pimcore.com/docs/6.x/User_Documentation/DataObjects/Grid_Configuration_Operators/Operators/TranslateValue.html). For a detailed example see [Website Translations](../../04_Query/11_Query_Samples/27_Sample_Translate_Values.md).

## Configuration

<div class="image-as-lightbox"></div>

![translate_value_config.png](../../../img/graphql/translate_value_config.png)

- **Label**: The label of the field.
- **Prefix**: The prefix for the translation key.

## Example

<div class="image-as-lightbox"></div>

![translate_value_example.png](../../../img/graphql/translate_value_example.png)

:::info

Note: Make sure to add the translation key to the translations in the Pimcore backend, using the admin domain.

:::

Request:
```graphql
{
  getCar(id: 82) {
    id,
    TranslatedName
  }
}
```

Response: 
```json
{
    "data": {
        "getCar": {
            "id": "82",
            "TranslatedName": "NameValuedAddedToTranslations"
        }
    }
}
```
