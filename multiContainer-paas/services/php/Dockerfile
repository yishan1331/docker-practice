ARG PHP_VERSION
FROM ${PHP_VERSION}

ARG TZ
ARG PHP_EXTENSIONS
ARG HTML
ARG PAAS_DASHBOARD

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions ${PHP_EXTENSIONS}

RUN apt-get -y update \
    && apt-get install -y vim-gtk less curl cron rsyslog

COPY var /var
RUN chown root:crontab /var/spool/cron/crontabs/root

COPY ${HTML} /var/www/html
COPY ${PAAS_DASHBOARD} /var/www/spdpaas/src/dashboard

RUN touch /var/log/cron.log

WORKDIR /var/www

CMD service cron start && tail -f /var/log/cron.log