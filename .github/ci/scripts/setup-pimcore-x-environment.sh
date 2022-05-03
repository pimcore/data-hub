#!/bin/bash

set -eu

mkdir -p var/config
mkdir -p bin

cp .github/ci/pimcore_x/files/.env .
cp -r .github/ci/pimcore_x/files/config/. config
cp -r .github/ci/pimcore_x/files/templates/. templates
cp -r .github/ci/pimcore_x/files/bin/console bin/console
chmod 755 bin/console
cp -r .github/ci/pimcore_x/files/kernel/. kernel
cp -r .github/ci/pimcore_x/files/public/. public
cp .github/ci/pimcore_x/files/extensions.template.php var/config/extensions.php