# Installation

## Minimum Requirements

* Pimcore >= 5.7

## Install

Install bundle via composer:
```bash 
composer require pimcore/data-hub
```

Enable bundle via console or extensions manager:
```bash
php bin/console pimcore:bundle:enable PimcoreDataHubBundle
php bin/console pimcore:bundle:install PimcoreDataHubBundle
```

Check if the bundle has been installed:
```bash
php bin/console pimcore:bundle:list
bin/console pimcore:bundle:list
+---------------------------------+---------+-----------+----+-----+-----+
| Bundle                          | Enabled | Installed | I? | UI? | UP? |
+---------------------------------+---------+-----------+----+-----+-----+
| PimcoreDataHubBundle            | ✔       | ✔         | ❌  | ✔   | ❌  |
+---------------------------------+---------+-----------+----+-----+-----+
```

## Required Backend User Permission
To access Datahub, user needs to meet one of following criteria:  
* be an `admin`
* have `plugin_datahub_config` permission