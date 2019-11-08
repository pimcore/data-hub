## Get Asset By Id

If you want to access a asset directly.
Note that the data will be base64 encoded and quite time-consuming to deliver.

Deeplink: http://pimcore-demo-basic.pim.zone/admin/login/deeplink?asset_4_image

### Request

Note that for the fullpath and the base64 encoded data you can specify a thumbnail config.

```
{
  getAsset(id: 4) {
    id
    # original
    fullpath,
    # thumbnail URL for exampleCover config
    assetThumb: fullpath(thumbnail: "exampleCover")
    # thumbnail URL for content config
    assetThumb2: fullpath(thumbnail: "content")
    type
    mimetype
    # original file size
    filesize
    # base 64 encoded "content" thumbnail
    data(thumbnail:"content")
  }
}

```

### Response

```
{
  "data": {
    "getAsset": {
        {
          "data": {
            "getAsset": {
              "id": "4",
              "fullpath": "/Car%20Images/jaguar/auto-automobile-automotive-192499.jpg",
              "assetThumb": "/Car%20Images/jaguar/image-thumb__4__exampleCover/auto-automobile-automotive-192499.webp",
              "assetThumb2": "/Car%20Images/jaguar/image-thumb__4__content/auto-automobile-automotive-192499.webp",
              "type": "image",
              "mimetype": "image/jpeg",
              "filesize": 113895,
              "data": "UklGRjh............."
            }
          }
        }
```


