#!/usr/bin/env bash

DIR=/var/www/html
cd ${DIR}

bash ./wait-for-it.sh db:3306 -- echo "db is up"

echo "------------------- 数据库创建 ---------------------"
echo "--------------------- migrate ${DIR} ------------------"
php artisan migrate --seed
echo "--------------------- migrate done ------------------"

echo "----------------- 文章初始数据添加 start -------------"
php artisan db:seed --class=ArticleTableSeeder
echo "----------------- 文章初始数据添加 done -------------"
