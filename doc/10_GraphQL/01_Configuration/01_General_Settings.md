# General Settings

![General Settings](../../img/graphql/general.png)

#### Some Aspects:
* `Active`: You can temporarily disable the configuration using the checkbox.
* `SQL Condition (Deprecated)`: You can add a condition all data object queries have to satisfy in addition to 
  the [workspace settings](./03_Security_Settings.md).

SQL Condition is currently deprecated but still enabled by default. If you want to disable it, you can do so in the symfony configuration tree:
```
pimcore_data_hub:
    graphql:
        allow_sqlObjectCondition: false
```
Please note that this option will be also removed in the next major version.