ARG PHP_VERSION
FROM ${PHP_VERSION}

ARG TZ
ARG PHP_EXTENSIONS
ARG API_HTML
ARG WEB_HTML
ARG PAAS_DASHBOARD

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions ${PHP_EXTENSIONS}

RUN apt-get -y update \
    && apt-get install -y vim-gtk less curl cron rsyslog

COPY var /var
RUN chown root:crontab /var/spool/cron/crontabs/root

RUN mkdir /var/www/html/api /var/www/html/web

COPY ${API_HTML} /var/www/html/api
COPY ${WEB_HTML} /var/www/html/web
COPY ${PAAS_DASHBOARD} /var/www/spdpaas/src/dashboard

RUN touch /var/log/cron.log

WORKDIR /var/www

CMD service cron start && tail -f /var/log/cron.log