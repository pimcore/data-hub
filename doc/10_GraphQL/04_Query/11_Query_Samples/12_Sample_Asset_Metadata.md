## Get Asset Metadata

![Metadata](../../../img/graphql/asset_metadata.png)

Deeplink: [http://pimcore-demo-basic.pim.zone/admin/login/deeplink?asset_4_image](http://pimcore-demo-basic.pim.zone/admin/login/deeplink?asset_4_image)

### Request

Get the custom asset metadata for language `de`

```graphql
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

```graphql
{
  "data": {
    "getAsset": {
      "id": "4",
      "fullpath": "/Car%20Images/jaguar/auto-automobile-automotive-192499.jpg",
      "type": "image",
      "mimetype": "image/jpeg",
      "filesize": 113895,
      "metadata": [
        {
          "name": "author",
          "type": "input",
          "data": "Mike",
          "language": ""
        },
        {
          "name": "authorLink",
          "type": "input",
          "data": "https://www.pexels.com/@mikebirdy",
          "language": ""
        },
        {
          "name": "copyright",
          "type": "input",
          "data": "Mike (https://www.pexels.com/@mikebirdy) | Pexels License",
          "language": ""
        },
        {
          "name": "license",
          "type": "input",
          "data": "Pexels License",
          "language": ""
        },
        {
          "name": "licensePath",
          "type": "input",
          "data": "https://www.pexels.com/photo-license/",
          "language": ""
        },
        {
          "name": "source",
          "type": "input",
          "data": "https://www.pexels.com/photo/auto-automobile-automotive-car-192499/",
          "language": ""
        }
      ]
    }
  }
}
```


