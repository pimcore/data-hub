---
title: Configuration & Deployment
description: Where Datahub stores its configurations, and how to deploy them.
---

# Configuration & Deployment

By default a configuration is stored as `var/config/data_hub/<name>.yaml`.
Additionally, a workspace permission index is kept in the database for better query performance when 
checking for permissions.

When deploying configurations following steps are necessary: 
- Deploy the configuration file `var/config/data_hub/<name>.yaml` - e.g. check it into your VCS and 
  deploy it with your deployment mechanisms. 

- Rebuild workspaces by running `datahub:configuration:rebuild-workspaces`  


Either call: 
```bash
datahub:configuration:rebuild-workspaces
```
to do that for all definitions, or:


```bash
datahub:configuration:rebuild-workspaces --configs=assets,events
```
for specific definitions.

:::warning

The command `datahub:graphql:rebuild-definitions` was removed in Datahub 2.0.0. Use
`datahub:configuration:rebuild-workspaces` instead.

:::

### Configuration Storage

The configuration user interface utilizes the `LocationAwareConfigRepository` for storing the configuration. In the symfony tree the storage location can be configured, possible values are
- `symfony-config` - write configs as Symfony Config as YAML files to `/var/config/data_hub/<name>.yaml`
- `settings-store` - write configs to the SettingsStore
- `disabled` - do not allow to edit/write configs at all

> Important: When using symfony-config write target, configs are written to Symfony
Config files (yaml), which are only getting revalidated in debug mode. So if you're changing configs in production you
won't see any update, because these configs are read only.

Details also see [Pimcore Docs](https://github.com/pimcore/pimcore/blob/2026.x/doc/08_Development_Details/01_Configuration/01_Configuration_Environments.md#configuration-storage-locations--fallbacks).

#### Example
```yaml
pimcore_data_hub:
    config_location: 
        data_hub:
            write_target:
                type: 'symfony-config'
                options:
                     directory: '/var/www/html/var/config/data_hub'
```

Additionally, it is also possible to define the configuration directly in a symfony configuration file without using
the configuration user interface. In this case, the configuration user interface is just read only.

#### Sample Configuration File
```yml 
pimcore_data_hub:
    configurations:
        <name>:
            general:
                active: true
                type: '<TYPE>'
                name: '<NAME>'
                description: '<DESCRIPTION>'
                group: '<GROUP>'
                modificationDate: <DATE>
                path: '<PATH>'
                createDate: <DATE>
            schema:
                queryEntities: {  }
                mutationEntities: {  }
                specialEntities:
                    document:
                        read: false
                        create: false
                        update: false
                        delete: false
...
