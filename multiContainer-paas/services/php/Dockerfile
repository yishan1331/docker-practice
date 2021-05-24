ARG PHP_VERSION
FROM ${PHP_VERSION}

ARG TZ
ARG PHP_EXTENSIONS
ARG API-HTML
ARG WEB-HTML
ARG PAAS-DASHBOARD

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions ${PHP_EXTENSIONS}

RUN apt-get -y update \
    && apt-get install -y vim-gtk less curl cron rsyslog

COPY var /var
RUN chown root:crontab /var/spool/cron/crontabs/root
RUN echo ${API-HTML}
COPY ${API-HTML} /var/www/html/api
RUN echo ${WEB-HTML}
COPY ${WEB-HTML} /var/www/html/web
RUN echo ${PAAS-DASHBOARD}
COPY ${PAAS-DASHBOARD} /var/www/spdpaas/src/dashboard

RUN touch /var/log/cron.log

WORKDIR /var/www

CMD service cron start && tail -f /var/log/cron.log