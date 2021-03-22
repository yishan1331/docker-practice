FROM php:7.2-fpm

RUN apt-get update -y && \
    apt-get install -y software-properties-common
RUN add-apt-repository -y ppa:ondrej/php
RUN apt-get update -y
RUN apt-get install -y php7.2-mbstring php7.2-xmlrpc php7.2-dev
RUN apt-get install -y php7.2-soap php7.2-gd php7.2-xml php7.2-cli php7.2-zip
