#!/bin/bash

docker-compose down -v --remove-orphans

docker-compose up -d

    docker-compose exec php-fpm .github/ci/scripts/setup-environment.sh

docker-compose exec php-fpm composer update
#docker-compose exec php-fpm composer update --prefer-lowest --prefer-stable

docker-compose exec php-fpm vendor/bin/codecept run -vv

printf "\n\n\n================== \n"
printf "Run 'docker-compose exec php-fpm vendor/bin/codecept run -vv' to re-run the tests.\n"
printf "Run 'docker-compose down -v --remove-orphans' to shutdown container and cleanup.\n\n"