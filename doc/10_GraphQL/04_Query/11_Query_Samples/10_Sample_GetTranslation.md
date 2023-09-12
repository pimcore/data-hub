# Get Translation By Key

If you want to access one translation.

### Request

```
{
    getTranslation (key: "BMW") {
            translations
            domain 
            key 
            type
        }
}

```

### Response

```
{
    "data": {
        "getTranslation": {
            "translations": "{\"de\":\"\",\"en\":\"\",\"fr\":\"\"}",
            "domain": "messages",
            "key": "BMW",
            "type": "simple"
        }
    }
}
```
