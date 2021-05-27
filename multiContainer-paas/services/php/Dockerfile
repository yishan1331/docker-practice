ARG PHP_VERSION
FROM ${PHP_VERSION}

ARG TZ
ARG PHP_EXTENSIONS
# ENV LANG=C.UTF-8

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions ${PHP_EXTENSIONS}

RUN apt-get -y update \
    && apt-get install -y vim-gtk less curl cron rsyslog

COPY var /var
RUN chown root:crontab /var/spool/cron/crontabs/root

RUN ln /usr/local/bin/php /var/www/

WORKDIR /var/www