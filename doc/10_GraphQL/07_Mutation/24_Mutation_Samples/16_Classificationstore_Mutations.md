# Classification Store Mutations

A Classification Store field selected as a mutation column becomes one input field named after the field. It carries the
whole store as one unit; there is no per-group or per-key column selection.

```graphql
mutation {
  updateCstest(
    id: 107
    input: {
      csfield: {
        groups: [
          {
            group: "dimensions"            # identifier, or groupId: 2
            language: "de"                 # defaults to "default"
            keys: [
              {
                key: "weight"              # identifier, or keyId: 5
                values: [
                  { value: "12" }
                  { value: "26", variant: "imperial", position: 1 }
                  { value: null, variant: "metric" }
                ]
              }
            ]
          }
        ]
        strict: false
      }
    }
  ) {
    success
    message
  }
}
```

* The write is a merge: only the slots (group, key, language, variant, position) named in the input are written, every
  other value of the store is left alone. An omitted key keeps its value, an explicit `null` clears exactly the slot it
  names.
* A group and a key are addressed by identifier or by numeric id, both forms can be mixed in one input. A `value` is
  given the way the editor sends it, the type of the key converts it.
* `variant` defaults to the default variant and `position` to 0. Two entries for the same slot are refused.
* Each key decides for itself whether it is kept per language: its own `localized` flag, or the flag of the field when it has none. A key that is not localized is written and read under `default` only (a named `language` is refused on write, and ignored by `features` and `qualifiedFeatures`), a localized key is addressed per language.
* An inactive group is activated by the write; with `strict: true` the write is refused instead.
* The whole mutation is refused, and nothing is saved, if a group or key does not resolve, a key is not part of the
  group, the group is not allowed on the field or belongs to another store, a key is disabled, or a key has a type that
  cannot be written. A disabled key still publishes its stored values, and refuses new ones.
* A `variant` the key does not declare is refused; keys that are not multiValue declare none besides the default.
* A variant or a position above 0 is refused on a store whose engine cannot hold qualified values.
* `omitMandatoryCheck: true` skips the mandatory check of the keys like it does for any other field.
* The writable types are the ones `csFeature` publishes, without Calculated Value, see
  [Classification Store queries](../../04_Query/11_Query_Samples/08_ClassificationStore.md).

## Value shapes

`value` is an untyped value and is converted to the stored form by the key's type, in the shape the editor sends. A
value that was written reads back unchanged.

| Type | `value` |
| --- | --- |
| `geopoint` | `{ latitude: 48.2, longitude: 16.3 }` |
| `geobounds` | `{ NElatitude, NElongitude, SWlatitude, SWlongitude }` |
| `geopolygon`, `geopolyline` | a list of `{ latitude, longitude }` |
| `link` | the link fields, e.g. `{ text: "Pimcore", path: "https://pimcore.com" }` |
| `image` | `{ id: 12 }`, the id of an image asset |
| `video` | `{ type: "asset", data: "/videos/clip.mp4" }`, the path of an asset; `type` can also name an external source |
| `manyToOneRelation` | `{ id: 3, type: "object" }`, the type being `object`, `asset` or `document` |
| `manyToManyRelation` | a list of `{ id, type }` of any element type |
| `manyToManyObjectRelation` | a list of `{ id, type: "object" }` |

A relation target that does not exist refuses the whole mutation and nothing is saved; `null` clears the slot.
