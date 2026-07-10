---
title: Installation
description: Install the Datahub bundle and grant access to the configuration panel.
---

# Installation

## Prerequisites

Datahub requires Pimcore 2026.1 or later and PHP 8.4 or 8.5. The Studio Backend bundle and the Studio UI bundle are
Composer dependencies and are pulled in automatically.

## Bundle Installation

1. Install the package:

```bash
composer require pimcore/data-hub
```

2. Enable the bundle in `config/bundles.php`:

```php
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
// ...

return [
    // ...
    PimcoreDataHubBundle::class => ['all' => true],
    // ...
];
```

3. Install the bundle:

```bash
bin/console pimcore:bundle:install PimcoreDataHubBundle
```

## User Permissions

Open the configuration panel in Pimcore Studio under **Automation & Integration** > **Data Hub Configuration**.

To reach it, a user needs to be an `admin`, or hold the `plugin_datahub_config` permission.

The installer creates three permissions in the Datahub permission category:

| Permission | Grants |
|---|---|
| `plugin_datahub_config` | Opens the Datahub configuration panel. |
| `plugin_datahub_admin` | Full access to every configuration, bypassing the per-configuration permissions. |
| `plugin_datahub_adapter_graphql` | Uses the GraphQL adapter. |

Access to an individual configuration is then resolved as follows:

1. An `admin` user, or a user holding `plugin_datahub_admin`, is always allowed.
2. Otherwise, if the configuration defines its own permissions in its **Permissions** tab, only those `create`, `read`,
   `update` and `delete` entries decide, per user and per role. The adapter permission is ignored.
3. If the configuration defines no permissions, the adapter permission (`plugin_datahub_adapter_<type>`) decides.

## Next Steps

- [Basic Principle](../02_Basic_Principle.md): how an endpoint configuration is put together.
- [GraphQL](../10_GraphQL/README.md): the default endpoint technology.
- [Upgrade Notes](./01_Upgrade_Notes.md): breaking changes per release.
