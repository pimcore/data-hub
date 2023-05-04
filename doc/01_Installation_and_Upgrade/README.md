# Installation 

## Bundle Installation
### For Pimcore >= 10.6
To install Pimcore Datahub for Pimcore 10.6 or higher, follow the three steps below:

1. Install the required dependencies:
```bash
composer require pimcore/data-hub
```

2. Make sure the bundle is enabled in the `config/bundles.php` file. The following lines should be added:

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

### For Pimcore 11

You need to follow the steps mentioned above and additionally run the following command:

```bash
composer require pimcore/admin-ui-classic-bundle
```

### For Older Versions
To install the Datahub bundle for older versions of Pimcore, please run the following commands instead:

```bash 
# For Pimcore >= 10.2:
composer require pimcore/data-hub
# For Pimcore < 10.2 replace the first line with: 
# composer require pimcore/data-hub:~1.0.11
bin/console pimcore:bundle:enable PimcoreDataHubBundle
bin/console pimcore:bundle:install PimcoreDataHubBundle
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
