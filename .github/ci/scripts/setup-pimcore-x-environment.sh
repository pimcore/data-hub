#!/bin/bash

set -eu

mkdir -p var/config
mkdir -p bin

cp -r .github/ci/pimcore_x/files/config/. config
cp -r .github/ci/pimcore_x/files/templates templates
cp -r .github/ci/pimcore_x/files/bin/console bin/console
chmod 755 bin/console
cp -r .github/ci/pimcore_x/files/kernel kernel
cp -r .github/ci/pimcore_x/files/app app
cp -r .github/ci/pimcore_x/files/public public
cp .github/ci/pimcore_x/files/extensions.template.php var/config/extensions.php
cp .github/ci/pimcore_x/files/.env ./

# move that to composer.json when only pimcore X
composer require codeception/module-symfony:^1.6.0 --no-update
composer require codeception/phpunit-wrapper:^9 --no-update
composer require codeception/module-asserts:^2 --no-update
