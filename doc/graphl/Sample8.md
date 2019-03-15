## Website Translations

Operator Config: 

![Data](../img/graphql/operator_translated1.png)

Website Translation Grid:

![Data](../img/graphql/operator_translated2.png)

### Request

```
{
  getUser(id: 49, defaultLanguage: "de") {
    # real Username
    username,
    # processed by the website translator with the prefix as defined in the export
    # config and the language as specified above
    translateduser
  }
}

```

### Response

Here you also see the use of aliases.

```
{
  "data": {
    "getUser": {
      "username": "john",
      "translateduser": "Johann"
    }
  }
}
```


