## Get Asset By Id

If you want to access a asset directy.
Note that the data will be base64 encoded and quite time-consuming to deliver.

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
      "id": "4",
      "fullpath": "/portal-sujets/slide-01.jpg",
      "assetThumb": "/portal-sujets/image-thumb__4__exampleCover/slide-01.jpeg",
      "assetThumb2": "/portal-sujets/image-thumb__4__content/slide-01.jpeg",
      "type": "image",
      "mimetype": "image/jpeg",
      "filesize": 169341,
      "data": "/9j/4AAQSkZJRgABAQA.....
      }
  }
}
```


