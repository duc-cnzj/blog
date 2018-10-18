#!/usr/bin/env bash

DIR=/var/www/html
cd ${DIR}

echo "----------------- phpunit -------------"
./vendor/bin/phpunit
echo "-------------- phpunit done -------------"

