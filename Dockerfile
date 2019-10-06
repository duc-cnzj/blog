FROM registry.cn-hangzhou.aliyuncs.com/duc-cnzj/base

LABEL maintainer="ducong"

RUN apt-get update \
    && apt-get install -y mariadb-client \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY . .

RUN composer install \
    && chown -R www-data: /var/www/html/storage /var/www/html/public
