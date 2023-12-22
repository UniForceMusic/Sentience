#!/usr/bin/env bash

composer install

php index.php database/create
php index.php database/init
php index.php database/migrate

exec $@
