FROM registry.cn-hangzhou.aliyuncs.com/duc-cnzj/application:7.3

LABEL maintainer="ducong"

RUN apt-get update \
    && apt-get install -y mysql-client-5.7 \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www/html
COPY . .

RUN composer install \
    && chown -R www-data: bootstrap/ public/ storage/
