#!/usr/bin/env bash

DIR=/var/www/html
cd ${DIR}

bash ./wait-for-it.sh db:3306 -- echo "db is up"

echo "------------------- 数据库创建 ---------------------"
echo "--------------- migrate ${DIR} ---------------"
php artisan migrate --seed
echo "------------------- migrate done -----------------"

sleep 5

echo "--------------------- elastic 数据索引创建 ------------------"
php artisan elastic:create-index App\\ES\\ArticleIndexConfigurator > /dev/null 2>&1
echo "--------------------- elastic 数据索引创建成功 ------------------"


echo "----------------- 文章初始数据添加 start -------------"
php artisan db:seed --class=ArticleTableSeeder
echo "----------------- 文章初始数据添加 done -------------"
