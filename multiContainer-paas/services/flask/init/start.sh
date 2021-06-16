#!/bin/sh
/etc/init.d/rsyslog start
crontab /var/spool/cron/crontabs/root
/etc/init.d/cron start

chown -R www-data:www-data /var/www/spdpaas

/wait-for-it.sh mysql:3306 postgres:5432 redis:6379 -t 10 -- uwsgi -i spdpaas_uwsgi.ini