# Translate Values

The following example translates the `AccessoryPart` condition value.

Deeplink: [http://pimcore-demo-basic.pim.zone/admin/login/deeplink?object_373_object](http://pimcore-demo-basic.pim.zone/admin/login/deeplink?object_373_object)

Operator Config: 

![Operator Config](../../../img/graphql/operator_translated1.png)

Website Translation Grid:

![Data](../../../img/graphql/operator_translated2.png) 

The idea in the following example is to use the website translation feature to convert
the value of the select field into a human-readable representation.

### Request

```graphql
{
  getAccessoryPart(id: 373, defaultLanguage: "de") {
    # real condition
    condition

    # processed by the website translator with the prefix as defined in the export
    # config and the language as specified above
    translatedCondition
  }
}
```

### Response

```json
{
  "data": {
    "getAccessoryPart": {
      "condition": "broken",
      "translatedCondition": "nicht mehr zu gebrauchen :-)"
    }
  }
}
```


