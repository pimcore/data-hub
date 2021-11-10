# Configuration & Deployment

The configuration is saved in `var/config/datahub-configurations.php`.
Additionally, a workspace permission index is kept in the database for better query performance when 
checking for permissions. 

When deploying configurations following steps are necessary: 
- Deploy configuration file `var/config/datahub-configurations.php` - e.g. check it into your VCS and 
  deploy it with your deployment mechanisms. 

- Rebuild workspace permission index by running `datahub:graphql:rebuild-definitions`  


Either call 
```bash
datahub:graphql:rebuild-definitions
``` 
to do that for all definitions, or


```bash
datahub:graphql:rebuild-definitions --definitions=newsapp,otherendpoint
```
for specific definitions.

### Configuration Storage

The configuration user interface utilizes the `LocationAwareConfigRepository` for storing the configuration. With
the environment variable `PIMCORE_WRITE_TARGET_DATA_HUB` the storage location can be configured, possible values are
- `symfony-config` - write configs as Symfony Config as YAML files to `/var/config/data-hub/<name>.yaml`
- `settings-store` - write configs to the SettingsStore
- `disabled` - do not allow to edit/write configs at all

Details also see [Pimcore Docs](https://pimcore.com/docs/pimcore/current/Development_Documentation/Deployment/Configuration_Environments.html#page_Configuration-Storage-Locations-Fallbacks).

#### Example
```env 
PIMCORE_WRITE_TARGET_DATA_HUB=symfony-config
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