#!/bin/sh

# starting services
/etc/init.d/rsyslog start
crontab /var/spool/cron/crontabs/root
/etc/init.d/cron start

cron -f &
docker-php-entrypoint php-fpm