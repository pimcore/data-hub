# Asset Thumbnail

Returns the selected thumbnail URL.

## Configuration

![Thumbnail Config](../../../img/graphql/thumbnail_config.png)

- **Attribute**: Name for the field to use in the query.
- **Thumbnail**: Select the desired thumbnail from the list.

## Example

![thumbnail_example.png](../../../img/graphql/thumbnail_example.png)

Request:
```graphql
{
  getCar(id: 82) {
    id,
    contentThumbnail
  }
}
```

Response:
```json
{
    "data": {
        "getCar": {
            "id": "82",
            "contentThumbnail": "/Car%20Images/ac%20cars/68/image-thumb__68__content/automotive-car-classic-149813.44c4f656.jpg"
        }
    }
}
```

[]