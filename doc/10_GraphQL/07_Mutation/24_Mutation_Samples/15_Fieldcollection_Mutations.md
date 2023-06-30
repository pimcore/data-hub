# Fieldcollection Mutations

A [Field Collection](https://pimcore.com/docs/6.x/Development_Documentation/Objects/Object_Classes/Data_Types/Fieldcollections.html)
is a list of items which can have different types.

Since there is no `Union` input type in GraphQL, the approach for Fieldcollection mutations is different.

The mutation processor expects a list of items for a specific type.
Within the list, all items are listed with their indices and data.

## Example

```
mutation {
	updateNews(
		id: 1143
		input: {
			content: {
				replace: false
				items: {
					NewsText: [{ index: 1, text: "news text" }]
					NewsCars: [
						{ index: 0, relatedCars: [{ type: "object", id: 318 }, { type: "object", id: 38 }] }
            { index: 2, title: "new cars", relatedCars: [{ type: "object", id: 156 }, { type: "object", id: 184 }] }
					]
				}
			}
		}
	) {
		success
		message
	}
}
```

## Notes

### Index

The index is optional. If no index is provided then an autoincrement is used.

### Replace vs. Overwrite

If `replace` is set to true (default is false) then the entire collection will be replaced.
Otherwise individual items will be overwritten.

