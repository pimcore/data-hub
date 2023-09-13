# Asset Mutations

## Create Asset

This will create an Asset with uploading data provided:
```graphql
mutation {
  createAsset(
    parentId: 1,
    filename: "foo.png",
    type: "image",
    input: {
      data: "ewogICAgImZpZWxkY29sbGVjdGlvbiI6IFsKICAgICAgICB7CiAgICAgICAgICAgICJwYXJlbn...."
    }) {
    success
    message
  }
}
```

## Update Asset

This will rename the Asset and update the data.
Request:
```graphql
mutation {
  updateAsset(id: 76, input: {filename:"newfilename",
  data:"iVBORw0KGgoAAAANSUhEUg....."}) {
    success
    message
    assetData {
         modificationDate
    }   
  }
}
```

## Delete Asset
```graphql
mutation {
  deleteAsset(id: 533) {
    success
    message
  }
}

or

mutation {
  deleteAsset(fullpath: "/Sample Content/Background Images/foo.png") {
    success
    message
  }
}

```