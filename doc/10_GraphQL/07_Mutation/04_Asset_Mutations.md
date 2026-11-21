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

The parent has to be covered by the workspace of the GraphQL configuration with the `create` right. This also applies
to a `parentId` given inside `input`.

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

The asset has to be covered by the workspace of the GraphQL configuration with the `update` right. When the input
contains a new `parentId`, the `create` right on the new parent is required as well. Otherwise the mutation answers
with `success: false`.

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