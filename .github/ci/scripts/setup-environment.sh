#!/bin/bash

set -eu

mkdir -p var/config
mkdir -p bin

cp .github/ci/files/.env .
cp -r .github/ci/files/config/. config
cp -r .github/ci/files/templates/. templates
cp -r .github/ci/files/bin/console bin/console
chmod 755 bin/console
cp -r .github/ci/files/kernel/. kernel
cp -r .github/ci/files/public/. public

if [ ${REQUIRE_ADMIN_BUNDLE} = true ]; then
    composer require -n --no-update pimcore/admin-ui-classic-bundle
fi
