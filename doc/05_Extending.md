---
title: Extending
description: Hook into the Studio API events dispatched by the Datahub configuration panel.
---

# Extending

## Studio API Events

The Datahub configuration panel is a Pimcore Studio plugin. Before one of its endpoints returns, the bundle dispatches
a pre-response event carrying the response schema. Listen for it to add your own data to the response.

| Event | Returned data |
|---|---|
| `pre_response.data_hub.configuration` | A configuration entry in the configuration tree listing: id, label, type, icon, adapter, writable flag, permissions, and child entries. |
| `pre_response.data_hub.configuration_detail` | The full detail of a single configuration: its name, configuration data, the current user's permissions on it, the supported GraphQL query and mutation data types, and the modification date. |
| `pre_response.data_hub.permission_user` | A user or role entry offered on a configuration's Permissions tab: id, display name, and element type (`user` or `role`). |
| `pre_response.data_hub.thumbnail` | A thumbnail definition entry offered for selection in a configuration: id and display name. |

See the Studio Backend bundle's
[Additional and Custom Attributes documentation](https://github.com/pimcore/studio-backend-bundle/blob/2026.x/doc/03_Extending/02_Additional_and_Custom_Attributes.md)
for the listener pattern.

:::warning

The endpoints themselves are not a supported public API. They are marked `@internal`, may change in any release
without a deprecation path, and must not be built against.

:::

## GraphQL Events

The GraphQL endpoint has its own, much richer event set covering query and mutation type building, listings,
execution, output cache, and permissions. See [GraphQL Events](./10_GraphQL/10_Events.md) for the full list.
