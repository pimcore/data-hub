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
                  { value: "26", variant: "imperial" }
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

* The write is a merge: only the slots (group, key, language, variant) named in the input are written, every
  other value of the store is left alone. An omitted key keeps its value, an explicit `null` clears exactly the slot it
  names.
* A group and a key are addressed by identifier or by numeric id, both forms can be mixed in one input. A `value` is
  given the way the editor sends it, the type of the key converts it.
* `variant` defaults to the default variant. A key holds one value per variant, two entries for the same slot are
  refused.
* Each key decides for itself whether it is kept per language: its own `localized` flag, or the flag of the field when it has none. A key that is not localized is written and read under `default` only (a named `language` is refused on write, and ignored by `features` and `qualifiedFeatures`), a localized key is addressed per language.
* An inactive group is activated by the write; with `strict: true` the write is refused instead.
* The whole mutation is refused, and nothing is saved, if a group or key does not resolve, a key is not part of the
  group, the group is not allowed on the field or belongs to another store, a key is disabled, or a key has a type that
  cannot be written. A disabled key still publishes its stored values, and refuses new ones.
* A `variant` the key does not declare is refused; keys that are not multiValue declare none besides the default.
* A variant other than the default is refused on a store whose engine cannot hold variants.
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
| `numericRange` | `{ minimum: 1, maximum: 7 }`, either bound may be left out and stays open |
| `quantityValueRange` | `{ minimum: 1, maximum: 7, unit: "cm" }`, the `unit` being the **id** of a quantity value unit, not its abbreviation; each member may be left out |
| `dateRange` | `{ startDate: 1700000000000, endDate: 1700086400000 }`, **milliseconds** since the epoch, both bounds required |
| `table` | `[["a", "b"], ["c", "d"]]`, a list of rows, each a list of cells |
| `user` | `12`, the id of a back office user |

A relation target that does not exist refuses the whole mutation and nothing is saved; `null` clears the slot.

### Refusals of the range, table and user types

The editmode conversion of these types answers an unusable value with a PHP warning, a type error, or silently with
`null`, which would clear the slot the write was meant to fill. Each case is refused with a message instead, and
nothing is saved:

* a `numericRange`, `quantityValueRange` or `dateRange` naming a member its type does not publish, or naming
  none of them - a misspelled bound would otherwise be written as `null`, and a value naming no bound at all
  would clear the key it was meant to fill;
* a bound of a `numericRange`, a `quantityValueRange` or a `dateRange` that is not a number - a
  `quantityValueRange` would otherwise store it as `0`;
* a `dateRange` with only one of its two bounds;
* a `unit` of a `quantityValueRange` that is not the id of an existing unit;
* a `user` that is not a positive numeric id, or is an id no user has - every non-numeric value would otherwise
  resolve to the user with the id `0`, and a missing id would clear the slot;
* a `table` that is not a list of rows of cells.

Two further notes on these types:

* A `table` is stored as it is given, as a table of a class field is. An all-blank row, and a row holding only `"0"`,
  are kept; the editmode conversion reads both as an empty table.
* A `numericRange` with a minimum and no maximum is refused **on save**, by the validation of the type itself, which
  compares the two bounds and reads a missing maximum as smaller. A maximum with no minimum is accepted. The
  asymmetry is the field type's own, on a class field as well as on a key.
