-!/usr/bin/env bash

DIR=/var/www/html
cd ${DIR}

bash ./shell/wait-for-it.sh db:3306 -- echo "db is up"

echo "------------------- 数据库创建 ---------------------"
php artisan migrate --seed
echo "----------------- 数据库创建 done -------------------"


echo "为了确保 ElasticSearch 索引创建成功，请等待 15s"

sleep 15

echo "-------------- elastic 数据索引创建 ----------------"
php artisan elastic:create-index App\\ES\\ArticleIndexConfigurator > /dev/null 2>&1
echo "-------------- elastic 数据索引创建 done ------------"


echo "-------------- 文章初始数据添加 start --------------"
php artisan db:seed --class=ArticleTableSeeder
echo "-------------- 文章初始数据添加 done --------------"
