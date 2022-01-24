# Testing

## Perform PHPStan Analysis

### data-hub only context

´´´bash
.github/ci/scripts/setup-pimcore-environment.sh
composer install
vendor/bin/phpstan analyse --memory-limit=-1
´´´

### Pimcore context

´´´bash
composer require "phpstan/phpstan:^1.4" --dev
vendor/bin/phpstan analyse -c vendor/pimcore/data-hub/phpstan.neon --memory-limit=-1
´´´
