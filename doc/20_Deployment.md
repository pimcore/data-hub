# Configuration & Deployment

The configuration by default is saved in `var/config/data-hub/example.yaml`.
Additionally, a workspace permission index is kept in the database for better query performance when 
checking for permissions.

When deploying configurations following steps are necessary: 
- Deploy configuration file `/var/config/.../example.yaml` - e.g. check it into your VCS and 
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

>Note: The command ```datahub:graphql:rebuild-definitions ``` is marked as deprecated and will be removed in a future release.   

### Configuration Storage

The configuration user interface utilizes the `LocationAwareConfigRepository` for storing the configuration. In the symfony tree the storage location can be configured, possible values are
- `symfony-config` - write configs as Symfony Config as YAML files to `/var/config/data_hub/<name>.yaml`
- `settings-store` - write configs to the SettingsStore
- `disabled` - do not allow to edit/write configs at all

> Important: When using symfony-config write target, configs are written to Symfony
Config files (yaml), which are only getting revalidated in debug mode. So if you're changing configs in production you
won't see any update, because these configs are read only.

Details also see [Pimcore Docs](https://pimcore.com/docs/pimcore/current/Development_Documentation/Deployment/Configuration_Environments.html#page_Configuration-Storage-Locations-Fallbacks).

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
                group: '<GROUP>
                sqlObjectCondition: '<CONDITION>'
                modificationDate: <DATE>
                path: '<PATH>'
                createDate: <DATE>'
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
