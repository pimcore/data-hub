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