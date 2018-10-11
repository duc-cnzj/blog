#!/usr/bin/env bash

DIR=/var/www/html

echo "--------------------- migrate ${DIR} ------------------"
cd ${DIR} && php artisan migrate --seed
echo "--------------------- migrate done ------------------"
