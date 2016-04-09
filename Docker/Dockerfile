FROM ubuntu:14.04

MAINTAINER hueypark <jaewan.huey.park@gmail.com>

RUN apt-get install -y language-pack-en-base

RUN apt-get install -y software-properties-common

RUN LC_ALL=en_US.UTF-8 add-apt-repository ppa:ondrej/php

RUN apt-get update

RUN apt-get install -y nginx
RUN apt-get install -y php7.0-fpm

ADD conf/php/php.ini /etc/php/7.0/php.ini
ADD conf/nginx/default /etc/nginx/sites-available/default

VOLUME ["/html"]

EXPOSE 80

ADD scripts/start.sh /start.sh
CMD ["/start.sh"]
