## Security

## Authentication

### Supported Methods

* API Key

## Workspace Settings

* Create
* Read

Similar to [Workspace Permissions](https://pimcore.com/docs/6.x/Development_Documentation/Administration_of_Pimcore/Users_and_Roles.html) 

![Settings](../img/graphql/security1.png)

* Update
* Delete

## Error Handling  - Configuration Values

The default behavior for associated/related objects, documents or assets you are not allowed to view is to simply null it out.
You can change that via a configuration setting.

* 1 = the entire query will fail
* 2 = null it out/skip it for multirelations (default)
 
```
pimcore_data_hub:
    graphql:
        not_allowed_policy: 2
```

It is also possible to disable the permission checks entirely by setting the configuration option
in the security definition tab.
