---
name: data-hub-graphql-configuration
description: "Use before proposing or changing a Data Hub GraphQL configuration: the document's sections, how a proposal merges, read-only access and the errors."
---

# GraphQL configurations

## How proposals work

1. `list_graphql_configs` finds the name; `get_graphql_config` reads the document. Never write one from memory.
2. `propose_graphql_config` with only the sections and keys you change. Everything you leave out keeps its current value.
3. Only `general`, `schema` and `workspaces` may be sent. `security` (API keys, authentication) and `permissions` are refused; a person changes them in the Data Hub editor.
4. Nothing is written: the proposal is a change request the user reviews. Once the review card appears, end your turn and wait for the decision.

A name nothing is stored under creates a new configuration: read a similar one and send its sections under the new name.

## Format

- `general`: `active`, `group`, `description`. Name and type are the configuration's own and ignored. Each key you send replaces that key only.
- `schema.queryEntities`, `schema.mutationEntities`: keyed by data object class name. Each entry is `{"id": "<Class>", "columnConfig": {"columns": [...]}}`, `id` equal to its key. A column is `{"isOperator": false, "attributes": {"attribute": "<field>", "dataType": "<field type>", ...}}`, or an operator with `"isOperator": true` and `attributes.class` plus `attributes.children`. Copy columns from a read document.
- `schema.specialEntities`: keyed by generic type, each `{"read": true, ...}`. The core types are `asset`, `asset_folder`, `asset_listing`, `document`, `document_folder`, `object_folder`, `translation`, `translation_listing`; installed bundles can add more, so take the names from a read document.
- Merge under these three maps: send only the entries you change, each one **complete** (it replaces the stored entry); `"<Entry>": null` removes one; entries you leave out stay; an empty map `{}` removes them all.
- `workspaces`: `asset`, `document`, `object`, each a list of `{"cpath": "/path", "read": true}`. A list is replaced whole, so send the full list for a type you change.
- Other keys and lists are replaced whole by what you send.
- Read access only: create, update or delete that the endpoint does not already grant (workspaces, mutation entities, generic types) is refused, and so is changing the columns of a mutation entity that may create or update.
- A new configuration needs at least one query entity and starts inactive unless `general.active` is `true`.

## Examples

Change the description:

```json
{"general": {"description": "Product feed for the shop"}}
```

Expose one more class and drop another, leaving the rest:

```json
{"schema": {"queryEntities": {
  "Car": {"id": "Car", "columnConfig": {"columns": [
    {"isOperator": false, "attributes": {"attribute": "name", "dataType": "input"}}
  ]}},
  "Manufacturer": null
}}}
```

New inactive configuration `shop-cars`:

```json
{"general": {"group": "shop", "description": "Cars"},
 "schema": {"queryEntities": {"Car": {"id": "Car", "columnConfig": {"columns": [
   {"isOperator": false, "attributes": {"attribute": "name", "dataType": "input"}}]}}}},
 "workspaces": {"object": [{"cpath": "/Cars", "read": true}]}}
```

## Errors

- "... is not part of a GraphQL configuration" / "... cannot be proposed": send only `general`, `schema`, `workspaces`.
- "needs "id": "X"": the entity's `id` must equal its key.
- "is not the name of a data object class": use a class this installation has.
- "is not a field of class", "needs "isOperator"", "is not a GraphQL ... operator": fix the column; copy one from a read document.
- "is not a generic type": use one of the listed types.
- "no ... exists at this path": a workspace `cpath` must be an existing element.
- "Write access cannot be proposed": propose `read` only; a person grants create, update and delete.
- "The input fields of a mutation entity": keep its `columnConfig` as stored.
- "taken by another Data Hub configuration": names are shared by all adapters; choose another.
- "leaves ... exactly as it is": nothing changed; tell the user nothing needs changing.
