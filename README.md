# Data Hub Bundle

## Minimum Requirements

* Pimcore >= 5.7

## Install
```bash 
composer require pimcore/data-hub
```

## Supported Channels

* ![](./doc/img/graphql/logo_mini.png) **[GraphQL](doc/GraphQL.md)**
* ![](./doc/img/csv/logo_small.png) CSV/XLS (coming soon...)
* ![](./doc/img/rest/logo_small.png) webservice (coming soon...)
* ...

## Adding a new configuration

![Configuration Overview](./doc/img/graphql/configuration3.png)

Choose the channel type

![Add Configuration](./doc/img/add_config.png)

And get the configuration done

Example for [GraphQL](doc/GraphQL.md)

## Required Backend User Permission

Either:
* `admin` role or
* `plugin_datahub_config`