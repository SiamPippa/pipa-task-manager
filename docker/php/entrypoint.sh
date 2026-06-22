#!/bin/sh
set -e

cd /var/www/html

if [ ! -e public/storage ] || [ ! -e public/storage/. ]; then
    rm -rf public/storage
    ln -s ../storage/app/public public/storage
fi

exec php-fpm
