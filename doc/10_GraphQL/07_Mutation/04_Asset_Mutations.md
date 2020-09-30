# Asset Mutations

## Create Asset

TODO add sample

## Update Asset

This will rename the asset + update the data.
Request:
```
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

TODO add sample
