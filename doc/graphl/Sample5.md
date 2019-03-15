## Get Asset Metadata

![Metadata](../img/graphql/asset_metadata.png)

### Request

Get the custom asset metadata for language `de`

```
{
  getAsset(id: 4, defaultLanguage: "de") {
    id
    fullpath
    type
    mimetype
    filesize
    metadata {
      name
      type
      data
      language
    }
  }
}

```

### Response

```
{
  "data": {
    "getAsset": {
      "id": "4",
      "fullpath": "/portal-sujets/slide-01.jpg",
      "type": "image",
      "mimetype": "image/jpeg",
      "filesize": 169341,
      "metadata": [
        {
          "name": "title",
          "type": "input",
          "data": "my title DE",
          "language": "de"
        },
        {
          "name": "copyright",
          "type": "input",
          "data": "copyright text DE",
          "language": "de"
        },
        {
          "name": "someref",
          "type": "object",
          "data": "52",
          "language": ""
        }
      ]
    }
  }
}
```


