# Get Translation Listing

### Request

Filter by domain "messages"

```
{
  getTranslationListing (domain: "messages") {
    edges {
      node {
        ... on translation {
                key, translations
            }
      }
    }
  }
}


```

### Response

```
{
    "data": {
        "getTranslationListing": {
            "edges": [
                {
                    "node": {
                        "key": "2-door berlinetta",
                        "translations":"{\"de\":\"2 T\ürer Sportcoup\é\",\"en\":\"\",\"fr\":\"\"}"
                    }
                },
                {
                    "node": {
                        "key": "2-door fastback coupé",
                        "translations":"{\"de\":\"2 T\ürer Coup\é\",\"en\":\"\",\"fr\":\"\"}"
                    }
                },
                {
                    "node": {
                        "key": "2-door hardtop",
                        "translations":"{\"de\":\"2 T\ürer Hardtop\",\"en\":\"\",\"fr\":\"\"}"
                    }
                },
                {
                    "node": {
                        "key": "2-door roadster",
                        "translations":"{\"de\":\"2 T\ürer Roadster\",\"en\":\"\",\"fr\":\"\"}"
                    }
                }
            ]
        }
    }
}
```
