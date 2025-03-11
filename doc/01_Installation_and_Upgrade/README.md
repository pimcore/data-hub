# Installation 

## Bundle Installation

To install Pimcore Datahub follow the three steps below:

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

## Required Backend User Permission
To access Datahub, user needs to meet one of following criteria:  
* be an `admin`
* have `plugin_datahub_config` permission
