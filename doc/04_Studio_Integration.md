---
title: Studio Integration
description: The Datahub configuration panel in Pimcore Studio, its permissions, and its internal API.
---

# Studio Integration

The Datahub configuration panel is a Pimcore Studio UI plugin. It lists the configurations, edits them, and embeds the
GraphQL explorer.

Open it in Pimcore Studio under **Automation & Integration** > **Data Hub Configuration**.

## Permissions

Opening the panel requires the `plugin_datahub_config` permission, or an `admin` user. Access to an individual
configuration is resolved from `plugin_datahub_admin`, the configuration's own **Permissions** tab, and the adapter
permission. See [Installation](./01_Installation_and_Upgrade/README.md#user-permissions).

## The Studio API

The panel is backed by endpoints under `/pimcore-studio/api/bundle/data-hub`, covering listing, reading, adding,
cloning, updating, deleting, importing and exporting configurations, plus the GraphQL explorer, and lookups for users
and thumbnails.

:::warning

These endpoints are marked `@internal`. They serve Pimcore Studio and may change in any release without a deprecation
path. Do not build integrations against them.

:::

Their current definitions and schemas are generated from the code and served by the Studio Backend bundle:

```
https://<your-pimcore-host>/pimcore-studio/api/docs
```

## The Public Endpoint

The API that Datahub exposes to other systems is the configured endpoint itself, not the Studio API. For the GraphQL
adapter it lives at:

```
https://<your-pimcore-host>/pimcore-graphql-webservices/<configuration-name>
```

See [GraphQL](./10_GraphQL/README.md) for its schema, security settings and query syntax, and
[Customize Endpoint URL](./10_GraphQL/01_Configuration/15_Customize_Endpoint_URL.md) to change the path.
