# Get Asset By Id

If you want to access an Asset directly.
Note that the data will be base64 encoded and quite time-consuming to deliver.

Deeplink: [http://pimcore-demo-basic.pim.zone/admin/login/deeplink?asset_4_image](http://pimcore-demo-basic.pim.zone/admin/login/deeplink?asset_4_image)

### Request

Note that for the fullpath and the base64 encoded data you can specify a thumbnail config.
You can use the `format` argument to retrieve the values for a specific format like `webp`.

```
{
  getAsset(id: 4) {
    id
    # original
    fullpath,
    # thumbnail URL for exampleCover config
    assetThumb: fullpath(thumbnail: "exampleCover")
    # thumbnail URL for content config
    assetThumb2: fullpath(thumbnail: "content", format: "webp")
    resolutions(thumbnail: "content", types: [2,5]) {
        resolution
        url
    }
    srcset(thumbnail: "content") {
        url
        descriptor
        # if types is not defined, then default resolutions @2x will be returned
        resolutions(types: [2,5]) {
            url
            resolution
        }
    }
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
              "assetThumb": "/Car%20Images/jaguar/4/image-thumb__4__exampleCover/auto-automobile-automotive-192499.jpg",
              "assetThumb2": "/Car%20Images/jaguar/4/image-thumb__4__content/auto-automobile-automotive-192499.webp",
              "resolutions": [
                {
                  "url": "//Car%20Images/jaguar/image-thumb__4__content/auto-automobile-automotive-192499~-~768w@2x.jpg",
                  "resolution": 2
                },
                {
                  "url": "//Car%20Images/jaguar/image-thumb__4__content/auto-automobile-automotive-192499~-~768w@5x.jpg",
                  "resolution": 5
                }
              ]
              "srcset": [
                {
                  "descriptor": "768w",
                  "url": "//Car%20Images/jaguar/image-thumb__4__content/auto-automobile-automotive-192499~-~768w.webp"
                  "resolutions": [
                    {
                      "url": "//Car%20Images/jaguar/image-thumb__4__content/auto-automobile-automotive-192499~-~768w@2x.webp",
                      "resolution": 2
                    },
                    {
                      "url": "//Car%20Images/jaguar/image-thumb__4__content/auto-automobile-automotive-192499~-~768w@5x.webp",
                      "resolution": 5
                    }
                  ]
                }
              ],
              "type": "image",
              "mimetype": "image/jpeg",
              "filesize": 113895,
              "data": "UklGRjh............."
            }
          }
        }
```
