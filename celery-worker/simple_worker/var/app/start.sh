#!/bin/sh
echo "@@@@@@@@@@@@@@@"
/etc/init.d/redis-server start
echo "redis-server start"
/etc/init.d/rsyslog start
echo "service rsyslog start"
/etc/init.d/cron start
echo "service cron start"
crontab /var/spool/cron/crontabs/root

celery -A celeryWorker multi start 20 --pidfile=/run/celery/%n%I.pid -l=${CELERYLOG} --logfile=/var/log/celery/%n%I.log -Q L-queue1 -c 1000 -P gevent -O fair
echo "start"

tail -F /var/log/syslog