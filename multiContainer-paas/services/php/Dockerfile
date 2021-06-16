ARG PHP_VERSION
FROM ${PHP_VERSION}

ARG TZ
ARG PHP_EXTENSIONS
# ENV LANG=C.UTF-8

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions ${PHP_EXTENSIONS}

RUN apt-get -y update \
    && apt-get install -y vim-gtk less curl cron rsyslog logrotate

COPY var /var
RUN chown root:crontab /var/spool/cron/crontabs/root
RUN chmod +x /var/www/crons.sh

WORKDIR /var/www

ENTRYPOINT ["./crons.sh"]
#https://stackoverflow.com/questions/44198700/getting-cron-to-run-on-php7-fpm-image

# CMD /etc/init.d/rsyslog start && crontab /var/spool/cron/crontabs/root && /etc/init.d/cron start && tail -F /var/log/syslog
#https://stackoverflow.com/questions/25135897/how-to-automatically-start-a-service-when-running-a-docker-container