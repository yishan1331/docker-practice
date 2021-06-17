#!/bin/sh
CELERY_LOG=$1
/etc/init.d/redis-server start
echo "redis-server start"
/etc/init.d/rsyslog start
echo "service rsyslog start"
/etc/init.d/cron start
echo "service cron start"
crontab /var/spool/cron/crontabs/root

celery -A celeryWorker multi start 20 --pidfile=/run/celery_%n.pid -l ${CELERY_LOG} --logfile=/var/log/celery/%n%I.log -Q L-queue1 -c 1000 -P gevent -O fair
echo "start"