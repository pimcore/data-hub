# Classification Store

## Supported Data Types

A Classification Store key supports every data type the same type supports on a plain object field. A key of such a
type shows up in `features` of its group and is a member of the `csFeature` union.

The data types a key can be read as are the ones with a registered `csfeaturequerytypegenerator`: one per supported
plain field type, plus `multiValue`, the Classification Store's own wrapper for qualified values. Calculated Value is
read only.

The following types are not supported and are skipped in `features`:

* Block, Fieldcollections, Classification Store: container types
* Reverse Object Relation, Reverse Many To Many Object Relation: computed from the owning side, not stored on a key
* URL Slug: needs a route registration keyed to the object and the field name
* Newsletter Active, Newsletter Confirmed: Customer Management Framework types, not core field types
* Table, Structured Table: their fields come from the columns of the field definition, not from a shared type

The legacy ids `href`, `multihref` and `multihrefMetadata` are the same types as `manyToOneRelation`,
`manyToManyRelation` and `advancedManyToManyRelation`. Relation keys are published as follows:

* `manyToOneRelation` and `manyToManyRelation` publish the `AnyTarget` union (a list for the latter)
* `manyToManyObjectRelation` publishes a union of the object classes enabled in the endpoint
* `advancedManyToManyRelation` and `advancedManyToManyObjectRelation` publish `element` (the same unions) and
  `metadata` (`element_metadata_item_key_value_pair`)

Targets the endpoint is not allowed to read are omitted from lists and resolve to `null` for a single target.

The data types a key can be written as are the same list without Calculated Value, see
[Classification Store Mutations](../../07_Mutation/24_Mutation_Samples/16_Classificationstore_Mutations.md).
A type that has no read generator (e.g. Table, Encrypted Field) is neither readable nor writable.

A `multiValue` key is published as `csFeatureMultiValue`, whose `values` carry every qualified value of the key
(`language`, `variant`, `position` and the `value`, typed as the inner type of the key). On a store whose engine cannot
hold qualified values `values` holds the single unqualified value.

## Qualified Values

`features` returns position 0 of the default variant of every key. `qualifiedFeatures` of `csGroup` returns the
same keys with all of their qualified values, the analogue of `getKeyValues()` of the store:

```graphql
csfield(language: "default") {
  name
  qualifiedFeatures {
    identifier
    values {
      language
      variant
      position
      value {
        ... on csFeatureInput { text }
      }
    }
  }
}
```

On a store whose engine cannot hold qualified values `qualifiedFeatures` is refused with an error naming the store;
`features` keeps working.


## Identifying Groups and Keys

Groups (`csGroup`) and keys (every `csFeature*` type and `csFeatureInterface`) publish two ways of
naming the configuration they stand for:

* `identifier` (`String`) - the identifier of the group or key configuration. It is part of the
  classification store configuration itself, so it stays the same when that configuration is moved
  to another environment.
* `id` (`Int`) - the numeric id of the configuration entry. It is handed out per installation, so it
  does not survive such a move. It is **deprecated** and will be removed in 2027.1.

A new integration should read `identifier`; `id` is only kept for integrations that already use it.

`identifier` is nullable: groups and keys created before identifiers were introduced can still have
none, and resolve to `null` until one is assigned to them in the classification store editor, where
a duplicate pre-flight check keeps the identifiers of a store unique.

Deprecated fields are hidden from introspection, so `id` is only listed when
`includeDeprecated: true` is passed:

```graphql
{
  __type(name: "csGroup") {
    fields(includeDeprecated: true) {
      name
      isDeprecated
      deprecationReason
    }
  }
}
```

## Request Example

`csfield` is the classification store field.

```graphql
{
{
  getCstest(id: 107) {
    id
    classname
    qvfield {
      unit {
        abbreviation
      }
      value
    }
    csfield(language: "default") {
      id
      identifier
      name
      description
      features {
        __typename
        ... on csFeatureInput {
          type
          id
          identifier
          name
          description
          text
        }
        ... on csFeatureQuantityValue {
          type
          id
          identifier
          description
          quantityvalue {
            unit {
              abbreviation
              longname
            }
            value
          }
        }
        ... on csFeatureTextarea {
          text
          name
        }
      }
    }
  }
}
```

## Response Example

```json
{
  "data": {
    "getCstest": {
      "id": "107",
      "classname": "cstest",
      "qvfield": {
        "unit": {
          "abbreviation": "cm"
        },
        "value": 3
      },
      "csfield": [
        {
          "id": 2,
          "identifier": "group1",
          "name": "Group1",
          "description": "Some group description",
          "features": [
            {
              "__typename": "csFeatureInput",
              "type": "input",
              "id": 2,
              "identifier": "my_key_definition",
              "name": "The name of my key definition",
              "description": "The description ...",
              "text": "C"
            },
            {
              "__typename": "csFeatureQuantityValue",
              "type": "quantityValue",
              "id": 3,
              "identifier": "my_quantityvalue_key",
              "description": "A quantityValue key definition",
              "quantityvalue": {
                "unit": {
                  "abbreviation": "mm",
                  "longname": "millimeter"
                },
                "value": 2
              }
            },
            {
              "__typename": "csFeatureTextarea",
              "text": "Hello, I am a textarea",
              "name": "key4"
            }
          ]
        }
      ]
    }
  }
}
```